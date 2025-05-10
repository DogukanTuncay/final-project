<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Admin\AiChatMessageServiceInterface;
use App\Http\Requests\Admin\AiChatMessageRequest;
use App\Http\Resources\Admin\AiChatMessageResource;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;

class AiChatMessageController extends Controller
{
    use ApiResponseTrait;
    
    protected $service;

    public function __construct(AiChatMessageServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Tüm mesajları listele
     */
    public function index(Request $request)
    {
        $items = $this->service->getWithPagination($request->all());
        return $this->successResponse(AiChatMessageResource::collection($items), 'admin.ai_chat_message.list.success');
    }

    /**
     * Yeni mesaj oluştur
     */
    public function store(AiChatMessageRequest $request)
    {
        $item = $this->service->create($request->validated());
        return $this->successResponse(new AiChatMessageResource($item), 'admin.ai_chat_message.create.success', 201);
    }

    /**
     * Mesaj detaylarını göster
     */
    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new AiChatMessageResource($item), 'admin.ai_chat_message.show.success');
    }

    /**
     * Mesajı güncelle
     */
    public function update(AiChatMessageRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new AiChatMessageResource($item), 'admin.ai_chat_message.update.success');
    }

    /**
     * Mesajı sil
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse(null, 'admin.ai_chat_message.delete.success');
    }

    /**
     * Belirli bir sohbete ait mesajları getir
     */
    public function getByChatId($chatId, Request $request)
    {
        $params = $request->all();
        $items = $this->service->getByChatId($chatId, $params);
        return $this->successResponse(AiChatMessageResource::collection($items), 'admin.ai_chat_message.list.success');
    }

    /**
     * Kullanıcıya ait mesajları getir
     */
    public function getUserMessages($userId, Request $request)
    {
        $params = $request->all();
        $items = $this->service->getUserMessages($userId, $params);
        return $this->successResponse(AiChatMessageResource::collection($items), 'admin.ai_chat_message.list.success');
    }

    /**
     * AI tarafından gönderilen mesajları getir
     */
    public function getAiMessages($chatId, Request $request)
    {
        $params = $request->all();
        $items = $this->service->getAiMessages($chatId, $params);
        return $this->successResponse(AiChatMessageResource::collection($items), 'admin.ai_chat_message.list.success');
    }
}