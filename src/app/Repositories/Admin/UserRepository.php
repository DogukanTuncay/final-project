<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\UserRepositoryInterface;
use App\Repositories\BaseRepository;
use App\Models\User;
class UserRepository extends BaseRepository
{
    public function __construct(User $user)
    {
        parent::__construct($user);
    }
}
