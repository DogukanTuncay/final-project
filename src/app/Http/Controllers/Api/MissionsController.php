<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\Services\Api\MissionsServiceInterface;
use App\Http\Resources\Api\MissionsResource;
use App\Http\Controllers\BaseController;
use App\Models\UserMissionProgress;
use Illuminate\Http\Request;

class MissionsController extends BaseController
{
    protected $service;

    public function __construct(MissionsServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm görevleri listele
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(MissionsResource::collection($items), 'responses.api.Missions.list.success');
    }

    /**
     * Belirli bir görevi getir
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $item = $this->service->find($id);
        
        if (!$item) {
            return $this->errorResponse(
                'responses.api.Missions.show.not_found',
                404
            );
        }
        
        return $this->successResponse(new MissionsResource($item), 'responses.api.Missions.show.success');
    }

    /**
     * Görevi tamamla
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function complete($id)
    {
        $mission = $this->service->find($id);
        
        if (!$mission) {
            return $this->errorResponse(
                'responses.api.Missions.complete.not_found',
                404
            );
        }
        
        $isCompleted = $this->service->complete($id);
        
        if ($isCompleted) {
            // Tamamlanan görev hakkında ek bilgi
            $progress = UserMissionProgress::where([
                'user_id' => auth()->id(),
                'mission_id' => $id
            ])->first();
            
            $response = [
                'mission' => new MissionsResource($mission),
                'progress' => $progress,
                'xp_earned' => $mission->xp_reward
            ];
            
            return $this->successResponse(
                $response,
                'responses.api.Missions.complete.success'
            );
        }

        return $this->errorResponse(
            'responses.api.Missions.complete.already_completed',
            409
        );
    }

    /**
     * Kullanıcının tamamlayabileceği görevleri al
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableForUser()
    {
        $missions = $this->service->getAvailableMissionsForUser();
        return $this->successResponse(MissionsResource::collection($missions), 'responses.api.Missions.available.success');
    }
    
    /**
     * Kullanıcının görev ilerlemelerini al
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function myProgress()
    {
        $user = auth()->user();
        
        if (!$user) {
            return $this->errorResponse('responses.api.auth.unauthenticated', 401);
        }
        
        $progressList = UserMissionProgress::with('mission')
            ->where('user_id', $user->id)
            ->get();
            
        $formattedProgress = $progressList->map(function ($progress) {
            return [
                'mission' => new MissionsResource($progress->mission),
                'current_amount' => $progress->current_amount,
                'required_amount' => $progress->mission->required_amount ?? 1,
                'is_completed' => $progress->isCompleted(),
                'completed_at' => $progress->completed_at,
                'progress_percentage' => min(100, ($progress->current_amount / ($progress->mission->required_amount ?? 1)) * 100)
            ];
        });
        
        return $this->successResponse($formattedProgress, 'responses.api.Missions.progress.success');
    }
}
