<?php

namespace App\Repositories\Api;

use App\Models\Mission;
use App\Models\UserMissionProgress;
use App\Models\User;
use App\Interfaces\Repositories\Api\MissionsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MissionsRepository implements MissionsRepositoryInterface
{
    protected Mission $model;
    protected UserMissionProgress $progressModel;

    public function __construct(Mission $model, UserMissionProgress $progressModel)
    {
        $this->model = $model;
        $this->progressModel = $progressModel;
    }

    /**
     * Tüm görevleri al
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function all()
    {
        return $this->model->where('is_active', true)->get();
    }

    /**
     * ID ile görev bul
     * 
     * @param int $id
     * @return Mission|null
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Kullanıcının tamamlayabileceği görevleri al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableMissionsForUser(int $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return collect([]);
        }
        
        // Önce aktif görevleri al
        $missions = $this->model->where('is_active', true)->get();
        
        // Görevleri filtrele
        return $missions->filter(function ($mission) use ($user) {
            $progress = $this->progressModel->where([
                'user_id' => $user->id,
                'mission_id' => $mission->id
            ])->first();
            
            // Eğer ilerleme yoksa, görev tamamlanabilir
            if (!$progress) {
                return true;
            }
            
            // Görev tipine göre tamamlanabilirliği kontrol et
            $missionType = $mission->type ?? 'one_time';
            
            switch ($missionType) {
                case 'one_time':
                    // Bir kerelik görevler tamamlandıysa artık kullanılamaz
                    return !$progress->isCompleted();
                    
                case 'daily':
                    // Günlük görevler bugün tamamlanmadıysa kullanılabilir
                    return !($progress->completed_at && $progress->completed_at->isToday());
                    
                case 'weekly':
                    // Haftalık görevler bu hafta tamamlanmadıysa kullanılabilir
                    return !($progress->completed_at && $progress->completed_at->isCurrentWeek());
                    
                default:
                    // Diğer tüm durumlarda, tamamlanmadıysa kullanılabilir
                    return !$progress->isCompleted();
            }
        });
    }

    /**
     * Görevi tamamlandı olarak işaretle
     * 
     * @param int $userId
     * @param int $missionId
     * @param int $amount
     * @return UserMissionProgress
     */
    public function markMissionAsCompleted(int $userId, int $missionId, int $amount = 1): UserMissionProgress
    {
        $progress = $this->progressModel->firstOrNew([
            'user_id' => $userId,
            'mission_id' => $missionId
        ]);
        
        $mission = $this->find($missionId);
        $requiredAmount = $mission->required_amount ?? 1;
        
        // İlerleme miktarını güncelle
        $progress->current_amount = ($progress->current_amount ?? 0) + $amount;
        
        // Gerekli miktara ulaşıldıysa tamamla
        if ($progress->current_amount >= $requiredAmount) {
            $progress->completed_at = now();
        }
        
        $progress->save();
        
        return $progress;
    }
    
    /**
     * Kullanıcının görev ilerlemesini al
     * 
     * @param int $userId
     * @param int $missionId
     * @return UserMissionProgress|null
     */
    public function getUserMissionProgress(int $userId, int $missionId): ?UserMissionProgress
    {
        return $this->progressModel->where([
            'user_id' => $userId,
            'mission_id' => $missionId
        ])->first();
    }
    
    /**
     * Kullanıcının tüm görev ilerlemelerini al
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUserMissionProgress(int $userId)
    {
        return $this->progressModel->with('mission')
            ->where('user_id', $userId)
            ->get();
    }

    /**
     * Belirli bir tipteki görevleri al
     * 
     * @param string $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByType(string $type)
    {
        return $this->model->where('type', $type)
            ->where('is_active', true)
            ->get();
    }
}
