<?php

namespace App\Http\Controllers\Api;

use App\Interfaces\Services\Api\MissionsServiceInterface;
use App\Http\Resources\Api\MissionsResource;
use App\Http\Controllers\BaseController;

class MissionsController extends BaseController
{
    protected $service;

    public function __construct(MissionsServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(MissionsResource::collection($items), 'responses.api.Missions.list.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new MissionsResource($item), 'responses.api.Missions.show.success');
    }



    public function complete($id)
    {
        $mission = $this->service->find($id);
    if (!$mission) {
        return $this->errorResponse(
            $mission,
            __('responses.api.Missions.complete.not_found'),
        );
    }
        $isCompleted = $this->service->complete($id);
    if ($isCompleted) {
        return $this->successResponse(
            $mission,
            'responses.api.Missions.complete.success'
        );
    }

    return $this->errorResponse(
        __('responses.api.Missions.complete.already_completed'),
        409,
        [],
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

}
