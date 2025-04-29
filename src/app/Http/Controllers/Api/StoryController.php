<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\StoryServiceInterface;
use App\Http\Resources\Api\StoryResource;
use App\Http\Requests\Api\StoryRequest;
use App\Traits\ApiResponseTrait;

class StoryController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(StoryServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(StoryRequest $request)
    {
        $params = $request->validated();
        $items = $this->service->getWithPagination($params);
        return $this->successResponse(StoryResource::collection($items), 'api.story.list.success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new StoryResource($item), 'api.story.show.success');
    }
}