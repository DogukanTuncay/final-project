<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\MissionsServiceInterface;
use App\Interfaces\Repositories\Api\MissionsRepositoryInterface;
use App\Models\User;
use App\Models\Mission;
use Tymon\JWTAuth\Facades\JWTAuth;


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


    public function getAvailableMissionsForUser()
    {
        return $this->repository->getAvailableMissionsForUser(JWTAuth::user()->id);
    }

    public function complete($id)
    {
        $mission = $this->repository->find($id); // Görevi bul
        $user = JWTAuth::user(); // Kullanıcıyı al
        // Görev zaten tamamlandıysa false döner
    $alreadyCompleted = $mission->users()
        ->where('user_id', $user->id)
        ->whereDate('mission_user.completed_at', now()->toDateString())
        ->exists();

    if ($alreadyCompleted) {
        return false;
    }

    // Görev kullanıcıya atanır
    $mission->users()->attach($user->id, [
        'completed_at' => now(),
        'xp_reward' => $mission->xp_reward,
    ]);

    // XP eklenir
    $user->addExperiencePoints($mission->xp_reward, 'mission', $mission->id);

    return true;
    }
}
