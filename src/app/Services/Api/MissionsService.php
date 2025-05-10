<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\MissionsServiceInterface;
use App\Interfaces\Repositories\Api\MissionsRepositoryInterface;
use App\Models\User;
use App\Models\Mission;
use App\Models\UserMissionProgress;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MissionsService implements MissionsServiceInterface
{
    protected MissionsRepositoryInterface $repository;

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
        
        return $this->repository->getAvailableMissionsForUser($user->id);
    }

    /**
     * Görevi tamamla
     * 
     * @param int $id Görev ID
     * @return bool Başarılıysa true, değilse false
     */
    public function complete($id)
    {
        return DB::transaction(function () use ($id) {
            $mission = $this->repository->find($id);
            $user = JWTAuth::user();
            
            // Görev veya kullanıcı yoksa işlem yapma
            if (!$mission || !$user) {
                return false;
            }
            
            // Görevin tipine göre tamamlanabilirliğini kontrol et
            $missionType = $mission->type ?? 'one_time';
            
            // Kullanıcının bu görevle ilgili mevcut ilerlemesini kontrol et veya oluştur
            $progress = UserMissionProgress::firstOrNew([
                'user_id' => $user->id,
                'mission_id' => $mission->id
            ]);
            
            // Görev tipi kontrolü
            switch ($missionType) {
                case 'one_time':
                    // Bu görev türü sadece bir kez tamamlanabilir
                    if ($progress->isCompleted()) {
                        return false; // Zaten tamamlanmış
                    }
                    break;
                
                case 'daily':
                    // Günlük görevler her gün tekrar tamamlanabilir
                    if ($progress->completed_at && $progress->completed_at->isToday()) {
                        return false; // Bugün zaten tamamlanmış
                    }
                    break;
                
                case 'weekly':
                    // Haftalık görevler her hafta tekrar tamamlanabilir
                    if ($progress->completed_at && $progress->completed_at->isCurrentWeek()) {
                        return false; // Bu hafta zaten tamamlanmış
                    }
                    break;
                
                default:
                    // Tanımlanmamış görev tipleri için one_time davranışı
                    if ($progress->isCompleted()) {
                        return false;
                    }
            }
            
            // İlerleme miktarını artır
            $incrementAmount = 1; // Varsayılan artış miktarı
            $progress->current_amount = ($progress->current_amount ?? 0) + $incrementAmount;
            
            // Gerekli miktara ulaşıldıysa tamamla
            $requiredAmount = $mission->required_amount ?? 1;
            
            if ($progress->current_amount >= $requiredAmount) {
                $progress->completed_at = now();
                
                // XP ödülünü kaydet
                $progress->xp_reward = $mission->xp_reward;
                
                // XP ödülünü ekle
                $user->addExperiencePoints($mission->xp_reward);
                
                // MissionCompleted event'ini tetikle
                try {
                    \Illuminate\Support\Facades\Log::info("MissionsService: Attempting to trigger MissionCompleted event for User ID: {$user->id}, Mission ID: {$mission->id}");
                    
                    if (class_exists('\\App\\Events\\MissionCompleted')) {
                        $completedEvent = new \App\Events\MissionCompleted(
                            $user,
                            $mission,
                            $progress,
                            $mission->xp_reward,
                            now()
                        );
                        
                        event($completedEvent);
                        
                        \Illuminate\Support\Facades\Log::notice("MissionsService: MissionCompleted event triggered successfully for User ID: {$user->id}, Mission ID: {$mission->id}", [
                            'event_class' => get_class($completedEvent),
                            'mission_title' => $mission->getTranslation('title', app()->getLocale()),
                            'xp_reward' => $mission->xp_reward
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
            }
            
            // Değişiklikleri kaydet
            $progress->save();
            
            return $progress->isCompleted();
        });
    }
}
