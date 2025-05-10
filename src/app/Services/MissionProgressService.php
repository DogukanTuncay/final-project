<?php

namespace App\Services;

use App\Models\User;
use App\Models\Mission;
use App\Models\UserMissionProgress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Loglama için
use App\Services\Api\EventService;
use App\Events\MissionCompleted;
use App\Traits\HandlesEvents;
class MissionProgressService
{
    use HandlesEvents;
    /**
     * Kullanıcının belirli bir olayla ilgili görevlerdeki ilerlemesini günceller.
     *
     * @param User $user Kullanıcı
     * @param string $eventType Tetikleyen olayın adı (örn: 'LessonCompleted')
     * @param Model|null $relatedModel Olayla doğrudan ilişkili model (örn: tamamlanan Lesson nesnesi)
     * @return array Tamamlanan görevlerin ID listesi
     */
    public function updateProgress(User $user, string $eventType, Model $relatedModel = null): array
    {
        // Olay tipinin kısa adını al (sınıf adı olarak geldiğinde)
        $eventName = $eventType;
        if (strpos($eventType, '\\') !== false) {
            $eventName = class_basename($eventType);
        }

        Log::info("MissionProgressService: {$eventName} olayı için görev ilerlemesi güncelleniyor. User ID: {$user->id}");

        // 1. Olay türüyle tetiklenen genel görevleri bul (completable olmayanlar)
        $generalMissions = Mission::where('trigger_event', $eventName)
                                  ->whereNull('completable_type')
                                  ->where('is_active', true)
                                  ->get();

        // 2. Olay türü ve ilişkili modelle tetiklenen spesifik görevleri bul
        $specificMissions = collect(); // Boş koleksiyon
        if ($relatedModel) {
            $specificMissions = Mission::where('trigger_event', $eventName)
                                       ->where('completable_type', $relatedModel->getMorphClass()) // Modelin morph class adını al
                                       ->where('completable_id', $relatedModel->id)
                                       ->where('is_active', true)
                                       ->get();
        }

        // İki listeyi birleştir ve benzersiz görevleri al
        $relevantMissions = $generalMissions->merge($specificMissions)->unique('id');

        if ($relevantMissions->isEmpty()) {
            return [];
        }

        $completedMissionIds = [];
        // Her bir ilgili görev için ilerlemeyi güncelle
        foreach ($relevantMissions as $mission) {
            $wasCompleted = $this->processMissionForUser($user, $mission);
            if ($wasCompleted) {
                $completedMissionIds[] = $mission->id;
            }
        }
        
        if (!empty($completedMissionIds)) {
            Log::info("MissionProgressService: {$user->id} ID'li kullanıcı için {$eventName} olayı ile görevler tamamlandı", [
                'completed_mission_count' => count($completedMissionIds)
            ]);
        }
        
        return $completedMissionIds;
    }

    /**
     * Belirli bir görev için kullanıcının ilerlemesini işler.
     *
     * @param User $user
     * @param Mission $mission
     * @return bool O anda görev tamamlandıysa true
     */
    protected function processMissionForUser(User $user, Mission $mission): bool
    {
        $completedNow = false;
        
        try {
            // Transaction içinde ilerlemeyi artır ve kontrol et
            DB::transaction(function () use ($user, $mission, &$completedNow) {
                // Daha önce tamamlanmış mı kontrol et
                $progress = UserMissionProgress::where('user_id', $user->id)
                    ->where('mission_id', $mission->id)
                    ->first();
                    
                // Tekrarlanabilir görevler için tarih kontrolü (daily/weekly)
                $shouldReset = false;
                $allowProgress = true;
                
                if ($progress && $progress->completed_at !== null) {
                    if ($mission->type === 'daily') {
                        // Eğer görev günlük ise ve bugün tamamlanmışsa, işlem yapma
                        if ($progress->completed_at->isToday()) {
                            $allowProgress = false;
                        } else {
                            // Eğer günlük görev daha önceki bir günde tamamlanmışsa, ilerlemeyi sıfırla
                            $shouldReset = true;
                        }
                    } elseif ($mission->type === 'weekly') {
                        // Eğer görev haftalık ise ve bu hafta tamamlanmışsa, işlem yapma
                        if ($progress->completed_at->isCurrentWeek()) {
                            $allowProgress = false;
                        } else {
                            // Eğer haftalık görev daha önceki bir haftada tamamlanmışsa, ilerlemeyi sıfırla
                            $shouldReset = true;
                        }
                    } else {
                        // one_time tipinde bir görev zaten tamamlanmışsa, işlem yapma
                        $allowProgress = false;
                    }
                }
                
                if (!$allowProgress) {
                    return;
                }
                
                // İlerleme kaydını al veya oluştur
                if ($progress) {
                    if ($shouldReset) {
                        // İlerlemeyi sıfırla (tekrarlanabilir görevler için)
                        $progress->current_amount = 0;
                        $progress->completed_at = null;
                    }
                    // Lock for update
                    $progress = UserMissionProgress::lockForUpdate()->find($progress->id);
                } else {
                    $progress = UserMissionProgress::lockForUpdate()->firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'mission_id' => $mission->id,
                        ],
                        [
                            'current_amount' => 0,
                        ]
                    );
                }
                
                // İlerlemeyi artır
                $progress->current_amount += 1;
                
                // Tamamlanma kontrolü
                if ($progress->current_amount >= $mission->required_amount && $progress->completed_at === null) {
                    // Tamamlama zamanını ayarla
                    $completionTime = now();
                    $progress->completed_at = $completionTime;
                    
                    Log::info("MissionProgressService: Görev tamamlandı. User ID: {$user->id}, Mission ID: {$mission->id}, Tip: {$mission->type}");
                    
                    // XP ödülünü kaydet
                    $progress->xp_reward = $mission->xp_reward;
                    // XP ver
                    if ($mission->xp_reward > 0) {
                        $user->addExperiencePoints($mission->xp_reward);
                    }
                    
                    // Progress kaydını önce kaydet
                    $progress->save();
                    
                    // MissionCompleted event'ini tetikle
                    try {
                        if (class_exists('\\App\\Events\\MissionCompleted')) {
                            $missionCompletedEvent = new MissionCompleted(
                                $user, 
                                $mission, 
                                $progress, 
                                $mission->xp_reward, 
                                $completionTime
                            );
                            
                            // Laravel event sistemini kullan
                            event($missionCompletedEvent);
                            
                            // Event verilerini API yanıtına ekle
                            $this->addCustomEventData($user, $mission, $progress);
                        } else {
                            Log::error("MissionProgressService: MissionCompleted event sınıfı bulunamadı");
                        }
                    } catch (\Exception $e) {
                        Log::error("MissionProgressService: Event tetiklenirken hata: " . $e->getMessage());
                    }
                    
                    $completedNow = true;
                } else {
                    // Progress kaydını kaydet
                    $progress->save();
                }
            }, 3); // Hata durumunda 3 kez yeniden dene
            
        } catch (\Exception $e) {
            Log::error("MissionProgressService: İşlem sırasında hata: " . $e->getMessage());
        }
        
        return $completedNow;
    }
    

    
    /**
     * Kullanıcının tamamladığı görevleri getirir
     *
     * @param User $user
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCompletedMissions(User $user)
    {
        return UserMissionProgress::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->with('mission')
            ->get();
    }
    
    /**
     * Belirli bir görevin tamamlanıp tamamlanmadığını kontrol eder
     *
     * @param User $user
     * @param int $missionId
     * @return bool
     */
    public function isMissionCompleted(User $user, int $missionId): bool
    {
        return UserMissionProgress::where('user_id', $user->id)
            ->where('mission_id', $missionId)
            ->whereNotNull('completed_at')
            ->exists();
    }

    /**
     * API yanıtı için özel event verisi ekler
     * 
     * @param User $user
     * @param Mission $mission
     * @param UserMissionProgress $progress
     * @return void 
     */
    protected function addCustomEventData(User $user, Mission $mission, UserMissionProgress $progress)
    {

        try {
            $eventData = [
                'mission_id' => $mission->id,
                'mission_title' => $mission->getTranslation('title', app()->getLocale()),
                'mission_description' => $mission->getTranslation('description', app()->getLocale(), ''),
                'user_id' => $user->id,
                'user_name' => $user->name,
                'xp_reward' => $mission->xp_reward,
                'current_amount' => $progress ? $progress->current_amount : null,
                'required_amount' => $mission->required_amount ?? 1,
                'mission_type' => $mission->type ?? 'one_time',
                'event_reason' => 'mission_completed',
                'event_source' => 'mission',
            ];
            // Mesajı görev tipine göre özelleştir
            $missionTitle = $mission->getTranslation('title', app()->getLocale());
            $messageParams = ['mission' => $missionTitle, 'xp' => $mission->xp_reward];
            
            // Görev tipine göre özel mesaj oluştur
            $messageKey = match($mission->type) {
                'daily' => 'events.daily_mission_completed',
                'weekly' => 'events.weekly_mission_completed',
                default => 'events.mission_completed'
            };
            
            $this->createEvent(
                'mission_completed',
                $eventData,
                __($messageKey, $messageParams),
                'mission'
            );
        } catch (\Exception $e) {
            Log::error("MissionProgressService: Event verileri eklenirken hata: " . $e->getMessage());
        }
    }

    /**
     * @deprecated Bu metod artık kullanılmıyor. Onun yerine addCustomEventData kullanın.
     */
    public function createMissionCompletedEvent(User $user, Mission $mission, UserMissionProgress $progress, int $xpReward, $completedAt = null)
    {
        $this->addCustomEventData($user, $mission, $progress);
    }
} 