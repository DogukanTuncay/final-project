<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Interfaces\Services\Api\AiChatMessageServiceInterface;
use App\Http\Resources\Api\AiChatMessageResource;
use App\Http\Requests\Api\AiChatMessageRequest;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
        return $this->successResponse(AiChatMessageResource::collection($items), 'api.ai_chat_message.list.success');
    }

    /**
     * ID'ye göre mesaj detayları
     */
    public function show($id)
    {
        $item = $this->service->findById($id);
        return $this->successResponse(new AiChatMessageResource($item), 'api.ai_chat_message.show.success');
    }

    /**
     * Yeni mesaj oluştur
     */
    public function store(AiChatMessageRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['is_from_ai'] = $data['is_from_ai'] ?? false;
        $data['is_active'] = $data['is_active'] ?? true;
        
        $item = $this->service->create($data);
        return $this->successResponse(new AiChatMessageResource($item), 'api.ai_chat_message.store.success', 201);
    }

    /**
     * Mesajı güncelle
     */
    public function update(AiChatMessageRequest $request, $id)
    {
        $item = $this->service->update($id, $request->validated());
        return $this->successResponse(new AiChatMessageResource($item), 'api.ai_chat_message.update.success');
    }

    /**
     * Mesajı sil
     */
    public function destroy($id)
    {
        $this->service->delete($id);
        return $this->successResponse([], 'api.ai_chat_message.delete.success');
    }

    /**
     * Belirli bir sohbete ait mesajları getir
     */
    public function getByChatId($chatId, Request $request)
    {
        $params = $request->all();
        $params['order_by'] = $params['order_by'] ?? 'created_at';
        $params['order_direction'] = $params['order_direction'] ?? 'asc';
        
        $items = $this->service->getByChatId($chatId, $params);
        return $this->successResponse(AiChatMessageResource::collection($items), 'api.ai_chat_message.list.success');
    }

    /**
     * Kullanıcıya ait mesajları getir
     */
    public function getUserMessages(Request $request)
    {
        $userId = auth()->id();
        $params = $request->all();
        $items = $this->service->getUserMessages($userId, $params);
        return $this->successResponse(AiChatMessageResource::collection($items), 'api.ai_chat_message.list.success');
    }

    /**
     * AI tarafından gönderilen mesajları getir
     */
    public function getAiMessages($chatId, Request $request)
    {
        $params = $request->all();
        $items = $this->service->getAiMessages($chatId, $params);
        return $this->successResponse(AiChatMessageResource::collection($items), 'api.ai_chat_message.list.success');
    }

    /**
     * Kullanıcı mesajını AI'ya gönderir ve yanıtı alır
     * Hem kullanıcı mesajı hem de AI yanıtı veritabanına kaydedilir
     *
     * @param AiChatMessageRequest $request
     * @return JsonResponse
     */
    public function send(AiChatMessageRequest $request): JsonResponse
    {
        // Validate edilmiş verileri al
        $data = $request->validated();
        $chatId = $data['ai_chat_id'];
        $message = $data['message'];
        
        // Güvenlik kontrolü
        $securityCheck = $this->service->performSecurityCheck($message);
        if (!$securityCheck['success']) {
            return $this->errorResponse(
                $securityCheck['messageKey'], 
                400, 
                null, 
                ['message' => $securityCheck['message']]
            );
        }
        
        // Kullanıcı mesajını kaydet
        $userMessage = $this->service->saveUserMessage($chatId, $message);
        
        // AI'ya gönder ve yanıt al
        $aiResponse = $this->service->processAndSaveAiResponse($chatId, $message);
        
        if (!$aiResponse['success']) {
            return $this->errorResponse(
                $aiResponse['messageKey'], 
                400, 
                null, 
                ['message' => $aiResponse['message']]
            );
        }
        
        // Sohbet mesajlarını al
        $chatMessages = $this->service->getByChatId($chatId, [
            'order_by' => 'created_at',
            'order_direction' => 'asc'
        ]);
        
        return $this->successResponse([
            'user_message' => new AiChatMessageResource($userMessage),
            'ai_message' => new AiChatMessageResource($aiResponse['data']),
            'chat_history' => AiChatMessageResource::collection($chatMessages)
        ], 'api.ai_chat_message.ai_response_success');
    }
}