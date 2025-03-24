<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\TrueFalseQuestionServiceInterface;
use App\Http\Requests\Admin\TrueFalseQuestionRequest;
use App\Http\Resources\Admin\TrueFalseQuestionResource;
use App\Traits\ApiResponseTrait;

class TrueFalseQuestionController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(TrueFalseQuestionServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(TrueFalseQuestionResource::collection($items), 'admin.TrueFalseQuestion.list.success');
    }

    public function store(TrueFalseQuestionRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new TrueFalseQuestionResource($item), 'admin.TrueFalseQuestion.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new TrueFalseQuestionResource($item), 'admin.TrueFalseQuestion.show.success');
    }

    public function update(TrueFalseQuestionRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new TrueFalseQuestionResource($item), 'admin.TrueFalseQuestion.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.TrueFalseQuestion.delete.success');
    }
}