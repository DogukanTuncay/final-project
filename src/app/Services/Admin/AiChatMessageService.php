<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\AiChatMessageServiceInterface;
use App\Interfaces\Repositories\Admin\AiChatMessageRepositoryInterface;

class AiChatMessageService implements AiChatMessageServiceInterface
{
    protected $repository;

    public function __construct(AiChatMessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function findById($id)
    {
        return $this->repository->findById($id);
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
}