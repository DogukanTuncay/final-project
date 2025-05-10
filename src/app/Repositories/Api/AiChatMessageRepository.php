<?php

namespace App\Repositories\Api;

use App\Models\AiChatMessage;
use App\Interfaces\Repositories\Api\AiChatMessageRepositoryInterface;

class AiChatMessageRepository implements AiChatMessageRepositoryInterface
{
    protected $model;

    public function __construct(AiChatMessage $model)
    {
        $this->model = $model;
    }

    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->where('slug', $slug)->firstOrFail();
    }

    public function getWithPagination(array $params)
    {
        $query = $this->model->query();

        // Filtreleme işlemleri
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }

        if (isset($params['ai_chat_id'])) {
            $query->where('ai_chat_id', $params['ai_chat_id']);
        }

        if (isset($params['user_id'])) {
            $query->where('user_id', $params['user_id']);
        }

        if (isset($params['is_from_ai'])) {
            $query->where('is_from_ai', $params['is_from_ai']);
        }
        
        // İlişkileri yükleme opsiyonu
        if (isset($params['with_relations']) && $params['with_relations']) {
            $query->with(['user', 'chat']);
        }

        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        // Sayfalama
        $perPage = $params['per_page'] ?? 15;
        return $query->paginate($perPage);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $item = $this->findById($id);
        $item->update($data);
        return $item;
    }

    public function delete($id)
    {
        $item = $this->findById($id);
        return $item->delete();
    }

    public function getByChatId($chatId, array $params = [])
    {
        $query = $this->model->where('ai_chat_id', $chatId);
        
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }
        
        // İlişkileri yükleme opsiyonu
        if (isset($params['with_relations']) && $params['with_relations']) {
            $query->with(['user', 'chat']);
        }
        
        // Sıralama işlemleri - varsayılan olarak kronolojik sıra (eski -> yeni)
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDirection = $params['order_direction'] ?? 'asc';
        $query->orderBy($orderBy, $orderDirection);
        
        // Sayfalama veya sınırlama
        if (isset($params['per_page'])) {
            $perPage = $params['per_page'];
            return $query->paginate($perPage);
        } elseif (isset($params['limit'])) {
            $limit = $params['limit'];
            return $query->limit($limit)->get();
        }
        
        return $query->get();
    }

    public function getUserMessages($userId, array $params = [])
    {
        $query = $this->model->where('user_id', $userId)
                            ->where('is_from_ai', false);
        
        if (isset($params['ai_chat_id'])) {
            $query->where('ai_chat_id', $params['ai_chat_id']);
        }
        
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }
        
        // İlişkileri yükleme opsiyonu
        if (isset($params['with_relations']) && $params['with_relations']) {
            $query->with(['chat']);
        }
        
        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);
        
        // Sayfalama veya sınırlama
        if (isset($params['per_page'])) {
            $perPage = $params['per_page'];
            return $query->paginate($perPage);
        } elseif (isset($params['limit'])) {
            $limit = $params['limit'];
            return $query->limit($limit)->get();
        }
        
        return $query->get();
    }

    public function getAiMessages($chatId, array $params = [])
    {
        $query = $this->model->where('ai_chat_id', $chatId)
                            ->where('is_from_ai', true);
        
        if (isset($params['is_active'])) {
            $query->where('is_active', $params['is_active']);
        }
        
        // İlişkileri yükleme opsiyonu
        if (isset($params['with_relations']) && $params['with_relations']) {
            $query->with(['user', 'chat']);
        }
        
        // Sıralama işlemleri
        $orderBy = $params['order_by'] ?? 'created_at';
        $orderDirection = $params['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);
        
        // Sayfalama veya sınırlama
        if (isset($params['per_page'])) {
            $perPage = $params['per_page'];
            return $query->paginate($perPage);
        } elseif (isset($params['limit'])) {
            $limit = $params['limit'];
            return $query->limit($limit)->get();
        }
        
        return $query->get();
    }
}