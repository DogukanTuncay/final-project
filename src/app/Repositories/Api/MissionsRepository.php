<?php

namespace App\Repositories\Api;

use App\Models\Mission;
use App\Interfaces\Repositories\Api\MissionsRepositoryInterface;
use Illuminate\Support\Facades\DB;
class MissionsRepository implements MissionsRepositoryInterface
{
    protected Mission $model;

    public function __construct(Mission $model)
    {
        $this->model = $model;
    }

         /**
     * Tüm görevleri al
     */
    public function all()
    {
        return $this->model->all();  // Eğer sayfalama isteniyorsa, burada paginate() kullanabilirsiniz.
    }

    /**
     * ID ile görev bul
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    public function getAvailableMissionsForUser(int $userId)
    {
        // Burada kullanıcı için henüz tamamlamadığı görevleri alabilirsin
        // Örnek olarak: tüm aktif görevler
        return $this->model->where('is_active', true)->get();
    }

    public function markMissionAsCompleted(int $userId, int $missionId): bool
    {
        // Pivot tablosu varsa burada kayıt işlemi yapılmalı
        return DB::table('mission_user')->updateOrInsert(
            ['user_id' => $userId, 'mission_id' => $missionId],
            ['completed_at' => now()]
        );
    }



    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}
