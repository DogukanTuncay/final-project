<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\CourseServiceInterface;
use App\Http\Requests\Api\CourseRequest;
use App\Http\Resources\Api\CourseResource;

class CourseController extends Controller
{
    protected $service;

    public function __construct(CourseServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return CourseResource::collection($items);
    }

    public function store(CourseRequest $request)
    {
        $item = $this->service->create($request->validated());
        return new CourseResource($item);
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return new CourseResource($item);
    }

    public function update(CourseRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return new CourseResource($item);
    }

    public function destroy($id)
    {
        return $this->service->delete($id);
    }
}