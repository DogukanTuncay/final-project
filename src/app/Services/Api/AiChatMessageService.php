<?php

namespace App\Services\Api;

use App\Interfaces\Services\Api\AiChatMessageServiceInterface;
use App\Interfaces\Repositories\Api\AiChatMessageRepositoryInterface;
use App\Models\Setting;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class AiChatMessageService implements AiChatMessageServiceInterface
{
    protected $repository;
    protected $httpClient;

    public function __construct(AiChatMessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->httpClient = new Client();
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
    }

    public function findBySlug($slug)
    {
        return $this->repository->findBySlug($slug);
    }

    public function getWithPagination(array $params)
    {
        return $this->repository->getWithPagination($params);
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function getByChatId($chatId, array $params = [])
    {
        // Varsayılan olarak mesajları oluşturma tarihine göre sıralayalım
        $params['order_by'] = $params['order_by'] ?? 'created_at';
        $params['order_direction'] = $params['order_direction'] ?? 'asc';
        
        return $this->repository->getByChatId($chatId, $params);
    }

    public function getUserMessages($userId, array $params = [])
    {
        return $this->repository->getUserMessages($userId, $params);
    }

    public function getAiMessages($chatId, array $params = [])
    {
        return $this->repository->getAiMessages($chatId, $params);
    }

    /**
     * Kullanıcının mesajını güvenlik kontrolünden geçirir
     * 
     * @param string $message
     * @return array
     */
    public function performSecurityCheck(string $message): array
    {
        // Yasaklanmış kelimeler listesi
        $bannedWords = Setting::getByKey('ai_banned_words', '');
        $bannedWordsArray = array_filter(explode(',', $bannedWords));
        
        // Mesaj uzunluğu kontrolü
        $maxLength = (int) Setting::getByKey('ai_max_message_length', 1000);
        if (strlen($message) > $maxLength) {
            return [
                'success' => false,
                'message' => "Message is too long (max {$maxLength} characters)",
                'messageKey' => 'api.ai_chat_message.too_long'
            ];
        }
        
        // Yasaklı kelime kontrolü
        foreach ($bannedWordsArray as $word) {
            $word = trim($word);
            if (!empty($word) && stripos($message, $word) !== false) {
                return [
                    'success' => false,
                    'message' => 'Message contains inappropriate content',
                    'messageKey' => 'api.ai_chat_message.inappropriate_content'
                ];
            }
        }
        
        // Başka güvenlik kontrolleri burada eklenebilir
        
        return [
            'success' => true,
            'message' => 'Message passed security check',
            'messageKey' => 'api.ai_chat_message.security_check_passed'
        ];
    }
    
    /**
     * Kullanıcı mesajını kaydeder
     * 
     * @param int $chatId
     * @param string $message
     * @return object
     */
    public function saveUserMessage(int $chatId, string $message): object
    {
        $userData = [
            'ai_chat_id' => $chatId,
            'user_id' => auth()->id(),
            'message' => $message,
            'is_from_ai' => false,
            'is_active' => true
        ];
        
        return $this->repository->create($userData);
    }
    
    /**
     * Mesajı AI'ya gönderir, yanıtı alır ve kaydeder
     * 
     * @param int $chatId
     * @param string $message
     * @return array
     */
    public function processAndSaveAiResponse(int $chatId, string $message): array
    {
        // AI özelliği aktif mi kontrol et
        $isEnabled = Setting::getByKey('ai_is_enabled', true);
        if (!$isEnabled) {
            return [
                'success' => false,
                'message' => 'AI chat feature is disabled',
                'messageKey' => 'api.ai_chat_message.disabled'
            ];
        }

        // API anahtarını al
        $apiKey = Setting::getByKey('ai_key_openai');
        if (!$apiKey) {
            Log::error('AI API key is not configured');
            return [
                'success' => false,
                'message' => 'AI API key is not configured',
                'messageKey' => 'api.ai_chat_message.no_api_key'
            ];
        }

        // Diğer ayarları al
        $apiUrl = Setting::getByKey('ai_api_url', 'https://api.openai.com/v1/chat/completions');
        $apiModel = Setting::getByKey('ai_api_model', 'gpt-3.5-turbo');
        $maxTokens = (int) Setting::getByKey('ai_max_tokens', 1000);
        $temperature = (float) Setting::getByKey('ai_temperature', 0.7);
        $currentLang = app()->getLocale();
        
        // Sistem talimatını al
        $systemPrompt = Setting::getByKey('ai_description') ?? "You are a helpful assistant. Answer concisely.";
        
        // OpenAI API için mesaj dizisi oluştur
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt]
        ];
        
        // Sohbetteki önceki mesajları al - kronolojik sırayla
        $previousMessages = $this->repository->getByChatId($chatId, [
            'order_by' => 'created_at',
            'order_direction' => 'asc',
            'per_page' => 20 // Son 20 mesajı al
        ]);
        
        // Mesajları API formatına çevir
        foreach ($previousMessages as $prevMessage) {
            $role = $prevMessage->is_from_ai ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $prevMessage->message];
        }
        
        // Mevcut kullanıcı mesajını ekle
        $messages[] = ['role' => 'user', 'content' => $message];
        
        try {
            // API isteği gönder
            $response = $this->httpClient->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $apiModel,
                    'messages' => $messages,
                    'max_tokens' => $maxTokens,
                    'temperature' => $temperature,
                ]
            ]);
            
            $responseBody = json_decode((string) $response->getBody(), true);
            
            if (isset($responseBody['choices'][0]['message']['content'])) {
                $aiResponse = $responseBody['choices'][0]['message']['content'];
                
                // AI yanıtını veritabanına kaydet
                $aiMessage = $this->repository->create([
                    'ai_chat_id' => $chatId,
                    'user_id' => null, // AI mesajları için user_id null
                    'message' => $aiResponse,
                    'is_from_ai' => true,
                    'is_active' => true
                ]);
                
                return [
                    'success' => true,
                    'data' => $aiMessage,
                    'message' => 'AI response received and saved',
                    'messageKey' => 'api.ai_chat_message.ai_response_success'
                ];
            }
            
            Log::error('Invalid AI API response', ['response' => $responseBody]);
            return [
                'success' => false,
                'message' => 'Invalid AI API response',
                'messageKey' => 'api.ai_chat_message.invalid_response'
            ];
            
        } catch (GuzzleException $e) {
            Log::error('AI API request failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'AI API request failed: ' . $e->getMessage(),
                'messageKey' => 'api.ai_chat_message.api_error'
            ];
        } catch (\Exception $e) {
            Log::error('Error sending message to AI', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Error sending message to AI: ' . $e->getMessage(),
                'messageKey' => 'api.ai_chat_message.general_error'
            ];
        }
    }
}