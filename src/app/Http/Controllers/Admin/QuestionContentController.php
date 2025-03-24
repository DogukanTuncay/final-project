<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\QuestionContentServiceInterface;
use App\Http\Requests\Admin\QuestionContentRequest;
use App\Http\Resources\Admin\QuestionContentResource;
use App\Traits\ApiResponseTrait;

class QuestionContentController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(QuestionContentServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(QuestionContentResource::collection($items), 'admin.QuestionContent.list.success');
    }

    public function store(QuestionContentRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new QuestionContentResource($item), 'admin.QuestionContent.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new QuestionContentResource($item), 'admin.QuestionContent.show.success');
    }

    public function update(QuestionContentRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new QuestionContentResource($item), 'admin.QuestionContent.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.QuestionContent.delete.success');
    }
}