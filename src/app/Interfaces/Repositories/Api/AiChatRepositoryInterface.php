<?php

namespace App\Interfaces\Repositories\Api;

interface AiChatRepositoryInterface
{
    /**
     * Kullanıcıya ait tüm sohbetleri getirir
     *
     * @param int $userId
     * @param array $params
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getUserChats(int $userId, array $params);

    /**
     * Yeni bir AI sohbeti oluşturur
     *
     * @param array $data
     * @return \App\Models\AiChat
     */
    public function create(array $data);

    /**
     * ID'ye göre sohbet bulur
     *
     * @param int $id
     * @return \App\Models\AiChat
     */
    public function findById(int $id);

    /**
     * Belirli bir kullanıcıya ait belirli bir sohbeti bulur
     *
     * @param int $userId
     * @param int $chatId
     * @return \App\Models\AiChat|null
     */
    public function findUserChat(int $userId, int $chatId);

    /**
     * Sohbeti günceller
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\AiChat
     */
    public function update(int $id, array $data);

    /**
     * Sohbeti siler
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id);
}