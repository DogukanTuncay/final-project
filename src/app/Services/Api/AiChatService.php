<?php

namespace App\Services\Api;

use App\Interfaces\Repositories\Api\AiChatRepositoryInterface;
use App\Interfaces\Services\Api\AiChatServiceInterface;
use App\Models\AiChat;
use App\Services\BaseService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class AiChatService extends BaseService implements AiChatServiceInterface
{
    public function __construct(AiChatRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    /**
     * Kullanıcıya ait sohbetleri getirir
     *
     * @param int $userId
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getUserChats(int $userId, array $params)
    {
        try {
            return $this->repository->getUserChats($userId, $params);
        } catch (\Exception $e) {
            Log::error('Kullanıcı sohbetleri getirilirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Yeni bir AI sohbeti oluşturur
     *
     * @param array $data
     * @return AiChat
     */
    public function create(array $data): AiChat
    {
        try {
            return $this->repository->create($data);
        } catch (\Exception $e) {
            Log::error('AI sohbeti oluşturulurken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * ID'ye göre sohbet bulur
     *
     * @param int $id
     * @return AiChat
     */
    public function findById(int $id): AiChat
    {
        try {
            return $this->repository->findById($id);
        } catch (\Exception $e) {
            Log::error('AI sohbeti bulunurken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Belirli bir kullanıcıya ait belirli bir sohbeti bulur
     *
     * @param int $userId
     * @param int $chatId
     * @return AiChat|null
     */
    public function findUserChat(int $userId, int $chatId): ?AiChat
    {
        try {
            return $this->repository->findUserChat($userId, $chatId);
        } catch (\Exception $e) {
            Log::error('Kullanıcı sohbeti bulunurken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sohbeti günceller
     *
     * @param int $id
     * @param array $data
     * @return AiChat
     */
    public function update($id, array $data): AiChat
    {
        try {
            return $this->repository->update($id, $data);
        } catch (\Exception $e) {
            Log::error('AI sohbeti güncellenirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Sohbeti siler
     *
     * @param int $id
     * @return bool
     */
    public function delete($id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            Log::error('AI sohbeti silinirken hata: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Kullanıcı için yeni bir AI sohbeti oluşturur veya mevcut olanı kullanır
     *
     * @param int $userId
     * @param string|null $title
     * @return AiChat
     */
    public function createOrGetUserChat(int $userId, ?string $title = null): AiChat
    {
        try {
            // Varsayılan başlık
            $defaultTitle = 'AI Sohbet #' . now()->format('Y-m-d H:i:s');
            
            $data = [
                'user_id' => $userId,
                'title' => $title ?? $defaultTitle,
                'is_active' => true
            ];
            
            return $this->repository->create($data);
        } catch (\Exception $e) {
            Log::error('Kullanıcı için AI sohbeti oluşturulurken hata: ' . $e->getMessage());
            throw $e;
        }
    }
}