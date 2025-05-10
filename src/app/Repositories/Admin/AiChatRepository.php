<?php

namespace App\Repositories\Admin;

use App\Models\AiChat;
use App\Interfaces\Repositories\Admin\AiChatRepositoryInterface;
use App\Repositories\BaseRepository;

class AiChatRepository extends BaseRepository implements AiChatRepositoryInterface
{
    public function __construct(AiChat $model)
    {
        parent::__construct($model);
    }
}