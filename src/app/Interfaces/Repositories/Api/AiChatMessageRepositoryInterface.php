<?php

namespace App\Interfaces\Repositories\Api;

interface AiChatMessageRepositoryInterface
{
    public function findById($id);
    public function findBySlug($slug);
    public function getWithPagination(array $params);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByChatId($chatId, array $params = []);
    public function getUserMessages($userId, array $params = []);
    public function getAiMessages($chatId, array $params = []);
    // Burada API için gerekli diğer metodları ekleyebilirsiniz
}