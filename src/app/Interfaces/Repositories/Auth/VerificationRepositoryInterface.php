<?php

namespace App\Interfaces\Repositories\Auth;
use App\Models\User;
interface VerificationRepositoryInterface
{
    public function createVerificationUrl(User $user);
    public function markEmailAsVerified(User $user);
    public function findUserById($id);
}
