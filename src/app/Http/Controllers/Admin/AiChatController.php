<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\AiChatServiceInterface;
use App\Http\Requests\Admin\AiChatRequest;
use App\Http\Resources\Admin\AiChatResource;
use App\Traits\ApiResponseTrait;

class AiChatController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(AiChatServiceInterface $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $items = $this->service->all();
        return $this->successResponse(AiChatResource::collection($items), 'admin.AiChat.list.success');
    }

    public function store(AiChatRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new AiChatResource($item), 'admin.AiChat.create.success');
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        return $this->successResponse(new AiChatResource($item), 'admin.AiChat.show.success');
    }

    public function update(AiChatRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new AiChatResource($item), 'admin.AiChat.update.success');
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.AiChat.delete.success');
    }
}