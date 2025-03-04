<?php

namespace App\Interfaces\Services\Auth;
use App\Models\User;
interface VerificationServiceInterface
{
    public function verify($id, $hash);
    public function resendVerificationEmail(User $user);

}
