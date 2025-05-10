<?php

namespace App\Repositories\Api;

use App\Interfaces\Repositories\Api\AiChatRepositoryInterface;
use App\Models\AiChat;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class AiChatRepository extends BaseRepository implements AiChatRepositoryInterface
{
    public function __construct(AiChat $model)
    {
        parent::__construct($model);
    }

    /**
     * Kullanıcıya ait tüm sohbetleri getirir
     *
     * @param int $userId
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getUserChats(int $userId, array $params)
    {
        $query = $this->model->forUser($userId)->active();
        
        // Sıralama
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);
        
        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        
        return $query->get();
    }

    /**
     * Yeni bir AI sohbeti oluşturur
     *
     * @param array $data
     * @return AiChat
     */
    public function create(array $data): AiChat
    {
        return $this->model->create($data);
    }

    /**
     * ID'ye göre sohbet bulur
     *
     * @param int $id
     * @return AiChat
     */
    public function findById(int $id): AiChat
    {
        return $this->model->findOrFail($id);
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
        return $this->model->where('id', $chatId)
            ->where('user_id', $userId)
            ->first();
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
        $chat = $this->model->findOrFail($id);
        $chat->update($data);
        return $chat;
    }

    /**
     * Sohbeti siler
     *
     * @param int $id
     * @return bool
     */
    public function delete($id): bool
    {
        $chat = $this->model->findOrFail($id);
        return $chat->delete();
    }
}