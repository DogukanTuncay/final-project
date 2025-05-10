<?php

namespace App\Services\Admin;

use App\Interfaces\Services\Admin\AiChatServiceInterface;
use App\Interfaces\Repositories\Admin\AiChatRepositoryInterface;
use App\Services\BaseService;

class AiChatService extends BaseService implements AiChatServiceInterface
{
    public function __construct(AiChatRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}