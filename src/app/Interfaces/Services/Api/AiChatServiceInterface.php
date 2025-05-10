<?php

namespace App\Interfaces\Services\Api;

use App\Models\AiChat;
use Illuminate\Pagination\LengthAwarePaginator;

interface AiChatServiceInterface
{
    /**
     * Kullanıcıya ait sohbetleri getirir
     *
     * @param int $userId
     * @param array $params
     * @return LengthAwarePaginator
     */
    public function getUserChats(int $userId, array $params);

    /**
     * Yeni bir AI sohbeti oluşturur
     *
     * @param array $data
     * @return AiChat
     */
    public function create(array $data): AiChat;

    /**
     * ID'ye göre sohbet bulur
     *
     * @param int $id
     * @return AiChat
     */
    public function findById(int $id): AiChat;

    /**
     * Belirli bir kullanıcıya ait belirli bir sohbeti bulur
     *
     * @param int $userId
     * @param int $chatId
     * @return AiChat|null
     */
    public function findUserChat(int $userId, int $chatId): ?AiChat;

    /**
     * Sohbeti günceller
     *
     * @param int $id
     * @param array $data
     * @return AiChat
     */
    public function update(int $id, array $data): AiChat;

    /**
     * Sohbeti siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Kullanıcı için yeni bir AI sohbeti oluşturur veya mevcut olanı kullanır
     *
     * @param int $userId
     * @param string|null $title
     * @return AiChat
     */
    public function createOrGetUserChat(int $userId, ?string $title = null): AiChat;
}