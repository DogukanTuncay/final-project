<?php

namespace App\Repositories\Auth;

use App\Interfaces\Repositories\Auth\VerificationRepositoryInterface;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
class VerificationRepository implements VerificationRepositoryInterface
{
    public function createVerificationUrl(User $user)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }

    /**
     * E-posta adresini doğrulanmış olarak işaretle
     *
     * @param User $user
     * @return bool
     */
    public function markEmailAsVerified(User $user)
    {
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            return true;
        }
        return false;
    }

    /**
     * ID'ye göre kullanıcı bul
     *
     * @param int $id
     * @return User
     */
    public function findUserById($id)
    {
        return User::findOrFail($id);
    }
}
