<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\MatchingQuestionServiceInterface;
use App\Http\Requests\Admin\MatchingQuestionRequest;
use App\Http\Resources\Admin\MatchingQuestionResource;
use App\Traits\ApiResponseTrait;

class MatchingQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(MatchingQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(MatchingQuestionResource::collection($items), 'admin.MatchingQuestion.list.success');
    }

    public function store(MatchingQuestionRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new MatchingQuestionResource($item), 'admin.MatchingQuestion.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new MatchingQuestionResource($item), 'admin.MatchingQuestion.show.success');
    }

    public function update(MatchingQuestionRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new MatchingQuestionResource($item), 'admin.MatchingQuestion.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.MatchingQuestion.delete.success');
    }
}