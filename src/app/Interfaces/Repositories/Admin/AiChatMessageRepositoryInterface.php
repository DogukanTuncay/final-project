<?php

namespace App\Interfaces\Repositories\Admin;

interface AiChatMessageRepositoryInterface
{
    public function findById($id);
    public function getWithPagination(array $params);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getByChatId($chatId, array $params = []);
    public function getUserMessages($userId, array $params = []);
    public function getAiMessages($chatId, array $params = []);
}