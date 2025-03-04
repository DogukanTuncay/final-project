<?php

namespace App\Services\Auth;

use App\Interfaces\Services\Auth\VerificationServiceInterface;
use App\Interfaces\Repositories\Auth\VerificationRepositoryInterface;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
class VerificationService implements VerificationServiceInterface
{
    protected $verificationRepository;

    public function __construct(VerificationRepositoryInterface $verificationRepository)
    {
        $this->verificationRepository = $verificationRepository;
    }

    public function verify($id, $hash)
    {
        $user = $this->verificationRepository->findUserById($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            throw new \Exception('Invalid verification link');
        }

        $verified = $this->verificationRepository->markEmailAsVerified($user);

        return [
            'user' => $user,
            'verified' => $verified
        ];
    }

    /**
     * Doğrulama e-postası URL'sini oluştur
     *
     * @param User $user
     * @return string Doğrulama URL'si
     * @throws \Exception
     */
    public function createVerificationUrl(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            throw new \Exception('Email already verified');
        }

        return $this->verificationRepository->createVerificationUrl($user);
    }

    /**
     * Doğrulama e-postasını yeniden gönder
     *
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function resendVerificationEmail(User $user)
    {
        if ($user->hasVerifiedEmail()) {
            throw new \Exception('Email already verified');
        }

        $verificationUrl = $this->verificationRepository->createVerificationUrl($user);

        Notification::send($user, new VerifyEmailNotification($verificationUrl));

        return true;
    }
}
