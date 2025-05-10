<?php

namespace App\Interfaces\Services\Api;

interface AiChatMessageServiceInterface
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
    
    /**
     * Kullanıcının mesajını güvenlik kontrolünden geçirir
     * 
     * @param string $message
     * @return array
     */
    public function performSecurityCheck(string $message): array;
    
    /**
     * Kullanıcı mesajını kaydeder
     * 
     * @param int $chatId
     * @param string $message
     * @return object
     */
    public function saveUserMessage(int $chatId, string $message): object;
    
    /**
     * Mesajı AI'ya gönderir, yanıtı alır ve kaydeder
     * 
     * @param int $chatId
     * @param string $message
     * @return array
     */
    public function processAndSaveAiResponse(int $chatId, string $message): array;
}