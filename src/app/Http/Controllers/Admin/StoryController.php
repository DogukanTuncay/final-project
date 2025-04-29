<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\StoryServiceInterface;
use App\Http\Requests\Admin\StoryRequest;
use App\Http\Resources\Admin\StoryResource;
use App\Traits\ApiResponseTrait;

class StoryController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(StoryServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(StoryResource::collection($items), 'admin.story.list.success');
    }

    public function store(StoryRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new StoryResource($item), 'admin.story.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new StoryResource($item), 'admin.story.show.success');
    }

    public function update(StoryRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new StoryResource($item), 'admin.story.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.story.delete.success');
    }
}