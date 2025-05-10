<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AiChatResource;
use App\Interfaces\Services\Api\AiChatServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AiChatController extends Controller
{
    use ApiResponseTrait;

    protected $service;

    public function __construct(AiChatServiceInterface $service)
    {
        $this->service = $service;
    }

    /**
     * Kullanıcıya ait sohbetleri listeler
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id();
        $params = $request->only(['order_by', 'order_direction', 'per_page']);
        
        $chats = $this->service->getUserChats($userId, $params);
        
        return $this->successResponse(
            AiChatResource::collection($chats)->response()->getData(true),
            'responses.api.ai_chat.list.success'
        );
    }

    /**
     * Yeni bir AI sohbeti oluşturur
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'nullable|string|max:255'
        ]);
        
        $userId = auth()->id();
        $chat = $this->service->createOrGetUserChat($userId, $request->title);
        
        return $this->successResponse(
            new AiChatResource($chat),
            'responses.api.ai_chat.create.success',
            Response::HTTP_CREATED
        );
    }

    /**
     * Belirli bir sohbeti gösterir
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $userId = auth()->id();
        $chat = $this->service->findUserChat($userId, $id);
        
        if (!$chat) {
            return $this->errorResponse(
                'responses.api.ai_chat.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->successResponse(
            new AiChatResource($chat),
            'responses.api.ai_chat.show.success'
        );
    }

    /**
     * Sohbeti günceller
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);
        
        $userId = auth()->id();
        $chat = $this->service->findUserChat($userId, $id);
        
        if (!$chat) {
            return $this->errorResponse(
                'responses.api.ai_chat.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        $updatedChat = $this->service->update($id, ['title' => $request->title]);
        
        return $this->successResponse(
            new AiChatResource($updatedChat),
            'responses.api.ai_chat.update.success'
        );
    }

    /**
     * Sohbeti siler
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $userId = auth()->id();
        $chat = $this->service->findUserChat($userId, $id);
        
        if (!$chat) {
            return $this->errorResponse(
                'responses.api.ai_chat.not_found',
                Response::HTTP_NOT_FOUND
            );
        }
        
        $this->service->delete($id);
        
        return $this->successResponse(
            null,
            'responses.api.ai_chat.delete.success'
        );
    }
}