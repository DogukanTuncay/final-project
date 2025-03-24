<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\ShortAnswerQuestionServiceInterface;
use App\Http\Requests\Admin\ShortAnswerQuestionRequest;
use App\Http\Resources\Admin\ShortAnswerQuestionResource;
use App\Traits\ApiResponseTrait;

class ShortAnswerQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(ShortAnswerQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(ShortAnswerQuestionResource::collection($items), 'admin.ShortAnswerQuestion.list.success');
    }

    public function store(ShortAnswerQuestionRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'admin.ShortAnswerQuestion.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'admin.ShortAnswerQuestion.show.success');
    }

    public function update(ShortAnswerQuestionRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new ShortAnswerQuestionResource($item), 'admin.ShortAnswerQuestion.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.ShortAnswerQuestion.delete.success');
    }
}