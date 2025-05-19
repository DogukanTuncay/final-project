<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\MissionsServiceInterface;
use App\Interfaces\Repositories\Api\MissionsRepositoryInterface;
use App\Models\User;
use App\Models\Mission;
use App\Models\UserMissionProgress;
use App\Models\UserMission;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Traits\HandlesEvents;
class MissionsService implements MissionsServiceInterface
{
    protected MissionsRepositoryInterface $repository;

    use HandlesEvents;


    public function __construct(MissionsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }
     /**
     * Tüm görevleri al
     */
    public function all()
    {
        return $this->repository->all();  // Repository'den tüm görevleri alıyoruz
    }

    /**
     * Belirli bir görevi ID ile bul
     */
    public function find($id)
    {
        return $this->repository->find($id);  // Repository'den görevi ID ile buluyoruz
    }

    /**
     * Kullanıcı için mevcut görevleri getir
     */
    public function getAvailableMissionsForUser()
    {
        $user = JWTAuth::user();
        if (!$user) {
            return collect([]);
        }
        
        $missions = $this->repository->getAvailableMissionsForUser($user->id);
        
        // Bugün tamamlanan görevleri filtrele
        $today = today();
        
        return $missions->filter(function ($mission) use ($user, $today) {
            // Görev tipine göre filtrele
            switch ($mission->type) {
                case Mission::TYPE_DAILY:
                    // Günlük görevler: bugün tamamlanmışsa çıkar
                    return !$user->hasMissionCompletedToday($mission->id);
                    
                case Mission::TYPE_WEEKLY:
                    // Haftalık görevler: bu hafta tamamlanmışsa çıkar
                    return !$user->completedMissions()
                        ->where('mission_id', $mission->id)
                        ->whereDate('completed_date', '>=', now()->startOfWeek())
                        ->whereDate('completed_date', '<=', now()->endOfWeek())
                        ->exists();
                    
                case Mission::TYPE_ONE_TIME:
                    // Tek seferlik görevler: hiç tamamlanmamışsa göster
                    return !$user->completedMissions()
                        ->where('mission_id', $mission->id)
                        ->exists();
                    
                case Mission::TYPE_MANUAL:
                    // Manuel görevler: hiç tamamlanmamışsa göster
                    return !$user->completedMissions()
                        ->where('mission_id', $mission->id)
                        ->exists();
                    
                default:
                    // Bilinmeyen tipler için tek seferlik gibi davran
                    return !$user->completedMissions()
                        ->where('mission_id', $mission->id)
                        ->exists();
            }
        });
    }

    /**
     * Belirli bir tipteki görevleri getir
     * 
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByType($type)
    {
        return $this->repository->getByType($type);
    }

    /**
     * Görevi tamamla
     * 
     * @param int $id Görev ID
     * @return bool|UserMission Başarılıysa tamamlama kaydı, değilse false
     */
    public function complete($id)
    {
        return DB::transaction(function () use ($id) {
            $mission = $this->repository->find($id);
            $user = JWTAuth::user();
            
            // Görev veya kullanıcı yoksa işlem yapma
            if (!$mission || !$user) {
                \Illuminate\Support\Facades\Log::warning("Geçersiz görev tamamlama isteği: Mission ID: {$id}, User: " . ($user ? $user->id : 'null'));
                return false;
            }
            
            // Görevin tipine göre tamamlanabilirliğini kontrol et
            $missionType = $mission->type ?? 'one_time';
            $today = today();
            
            // Görev tipi kontrolü
            switch ($missionType) {
                case Mission::TYPE_ONE_TIME:
                    // Bu görev türü sadece bir kez tamamlanabilir
                    if ($user->completedMissions()->where('mission_id', $mission->id)->exists()) {
                        \Illuminate\Support\Facades\Log::info("Tek seferlik görev zaten tamamlanmış: User ID: {$user->id}, Mission ID: {$id}");
                        return false; // Zaten tamamlanmış
                    }
                    break;
                
                case Mission::TYPE_DAILY:
                    // Günlük görevler her gün tekrar tamamlanabilir
                    // Sadece bugün tamamlanmış mı kontrolü yapalım
                    if ($user->hasMissionCompletedToday($mission->id)) {
                        \Illuminate\Support\Facades\Log::info("Günlük görev bugün zaten tamamlanmış: User ID: {$user->id}, Mission ID: {$id}");
                        return false; // Bugün zaten tamamlanmış
                    }
                    break;
                
                case Mission::TYPE_WEEKLY:
                    // Haftalık görevler her hafta tekrar tamamlanabilir
                    if ($user->completedMissions()
                            ->where('mission_id', $mission->id)
                            ->whereDate('completed_date', '>=', now()->startOfWeek())
                            ->whereDate('completed_date', '<=', now()->endOfWeek())
                            ->exists()) {
                        \Illuminate\Support\Facades\Log::info("Haftalık görev bu hafta zaten tamamlanmış: User ID: {$user->id}, Mission ID: {$id}");
                        return false; // Bu hafta zaten tamamlanmış
                    }
                    break;
                
                case Mission::TYPE_MANUAL:
                    // Manuel görevler bir kez tamamlanabilir
                    if ($user->completedMissions()->where('mission_id', $mission->id)->exists()) {
                        \Illuminate\Support\Facades\Log::info("Manuel görev zaten tamamlanmış: User ID: {$user->id}, Mission ID: {$id}");
                        return false; // Zaten tamamlanmış
                    }
                    break;
                
                default:
                    // Tanımlanmamış görev tipleri için one_time davranışı
                    if ($user->completedMissions()->where('mission_id', $mission->id)->exists()) {
                        \Illuminate\Support\Facades\Log::warning("Tanımsız görev tipi ({$missionType}) zaten tamamlanmış: User ID: {$user->id}, Mission ID: {$id}");
                        return false;
                    }
            }
            
            // İlerleme kaydını al veya oluştur (takip için kullanılacak)
            $progress = UserMissionProgress::firstOrNew([
                'user_id' => $user->id,
                'mission_id' => $mission->id
            ]);
            
            // İlerleme miktarını artır
            $incrementAmount = 1; // Varsayılan artış miktarı
            $progress->current_amount = ($progress->current_amount ?? 0) + $incrementAmount;
            
            // Gerekli miktara ulaşıldıysa tamamla
            $requiredAmount = $mission->required_amount ?? 1;
            
            if ($progress->current_amount >= $requiredAmount) {
                // XP ödülünü kaydet
                $xpReward = $mission->xp_reward;
                
                // XP ödülünü ekle
                $user->addExperiencePoints($xpReward);
                
                // Tamamlama kaydı oluştur
                $completion = new UserMission([
                    'user_id' => $user->id,
                    'mission_id' => $mission->id,
                    'xp_earned' => $xpReward,
                    'completed_date' => today()
                ]);
                
                $completion->save();
                
                // Önbelleği temizle
                \Illuminate\Support\Facades\Cache::forget("user_{$user->id}_missions_completed");
                \Illuminate\Support\Facades\Cache::forget("user_{$user->id}_missions_progress");
                
                // MissionCompleted event'ini tetikle
                try {
                    \Illuminate\Support\Facades\Log::info("MissionsService: Attempting to trigger MissionCompleted event for User ID: {$user->id}, Mission ID: {$mission->id}");
                    
                    if (class_exists('\\App\\Events\\MissionCompleted')) {
                        $completedEvent = new \App\Events\MissionCompleted(
                            $user,
                            $mission,
                            $progress,
                            $xpReward,
                            now()
                        );
                        
                        $this->createEvent(
                            'mission_completed',
                            $completedEvent->toArray(),
                            __('events.mission_completed', ['mission' => $mission->getTranslation('title', app()->getLocale()), 'xp' => $xpReward]),
                            'mission'
                        );

                                                
                        \Illuminate\Support\Facades\Log::notice("MissionsService: MissionCompleted event triggered successfully for User ID: {$user->id}, Mission ID: {$mission->id}", [
                            'event_class' => get_class($completedEvent),
                            'mission_title' => $mission->getTranslation('title', app()->getLocale()),
                            'xp_reward' => $xpReward
                        ]);
                    } else {
                        \Illuminate\Support\Facades\Log::error("MissionsService: MissionCompleted event class not found");
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("MissionsService: Event tetiklenirken hata: " . $e->getMessage(), [
                        'exception' => $e,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
                
                // İlerleme kaydını kaydet (takip için)
                $progress->delete();
                
                return $completion;
            }
            
            // Değişiklikleri kaydet
            $progress->save();
            
            return false; // Henüz tamamlanmadı
        });
    }
}
