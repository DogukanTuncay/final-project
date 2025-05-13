<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\Services\Api\MissionsServiceInterface;
use App\Http\Resources\Api\MissionsResource;
use App\Http\Controllers\BaseController;
use App\Models\UserMissionProgress;
use App\Models\UserMission;
use App\Models\Mission;
use Illuminate\Http\Request;
use App\Services\Api\EventService;
use App\Traits\HandlesEvents;

class MissionsController extends BaseController
{
    use HandlesEvents;
    
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

        if($mission->type != Mission::TYPE_MANUAL){
            return $this->errorResponse(
                'responses.api.Missions.complete.manual',
                400
            );
        }
        
        $result = $this->service->complete($id);
        
        if ($result) {
          
            
            // EventService'den eventleri al
            
            $eventService = app(EventService::class);
            $events = $eventService->getEvents();
            $eventService->clearEvents();
            $response = [
                'mission' => new MissionsResource($mission),
                'completion' => $result instanceof UserMission ? $result : null,
                'events' => $events
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
        
        // İlerleme kayıtları
        $progressList = UserMissionProgress::with('mission')
            ->where('user_id', $user->id)
            ->get();
            
        // Tamamlama kayıtları
        $completionsList = UserMission::with('mission')
            ->where('user_id', $user->id)
            ->orderBy('completed_date', 'desc')
            ->get();
            
        $formattedProgress = $progressList->map(function ($progress) use ($completionsList) {
            $mission = $progress->mission;
            
            // Bu görev için tamamlama kayıtları
            $completions = $completionsList->where('mission_id', $mission->id)->values();
            $todayCompletion = $completions->first(function($completion) {
                return $completion->completed_date->isToday();
            });
            
            // Görev tipine göre tamamlanma durumu
            $isCompleted = false;
            
            switch ($mission->type) {
                case Mission::TYPE_DAILY:
                    // Günlük görevler için bugün tamamlanmış mı?
                    $isCompleted = $todayCompletion !== null;
                    break;
                    
                case Mission::TYPE_WEEKLY:
                    // Haftalık görevler için bu hafta tamamlanmış mı?
                    $isCompleted = $completions->contains(function($completion) {
                        return $completion->completed_date->isCurrentWeek();
                    });
                    break;
                    
                case Mission::TYPE_ONE_TIME:
                case Mission::TYPE_MANUAL:
                default:
                    // Tek seferlik görevler için herhangi bir zaman tamamlanmış mı?
                    $isCompleted = $completions->isNotEmpty();
                    break;
            }
            
            return [
                'mission' => new MissionsResource($mission),
                'current_amount' => $progress->current_amount,
                'required_amount' => $mission->required_amount ?? 1,
                'is_completed' => $isCompleted,
                'completions' => $completions->map(function($completion) {
                    return [
                        'id' => $completion->id,
                        'completed_date' => $completion->completed_date,
                        'xp_earned' => $completion->xp_earned
                    ];
                }),
                'today_completion' => $todayCompletion ? [
                    'id' => $todayCompletion->id,
                    'completed_date' => $todayCompletion->completed_date,
                    'xp_earned' => $todayCompletion->xp_earned
                ] : null,
                'progress_percentage' => min(100, ($progress->current_amount / ($mission->required_amount ?? 1)) * 100)
            ];
        });
        
        return $this->successResponse($formattedProgress, 'responses.api.Missions.progress.success');
    }

    /**
     * Belirli bir tipteki görevleri getir
     * 
     * @param string $type
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByType($type)
    {
        $missions = $this->service->getByType($type);
        return $this->successResponse(MissionsResource::collection($missions), 'responses.api.Missions.list.success');
    }
}
