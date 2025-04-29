<?php

namespace App\Services\Auth;

use App\Interfaces\Repositories\Auth\AuthRepositoryInterface;
use App\Interfaces\Services\Auth\AuthServiceInterface;
use App\Services\BaseService;
use App\Classes\UsernameGenerator;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Interfaces\Services\Auth\VerificationServiceInterface;
use Illuminate\Support\Facades\DB;
use App\Notifications\VerifyEmailNotification;

class AuthService extends BaseService implements AuthServiceInterface
{
    protected $authRepository;
    protected $verificationService;

    public function __construct(
        AuthRepositoryInterface $authRepository,
        VerificationServiceInterface $verificationService
    ) {
        $this->authRepository = $authRepository;
        $this->verificationService = $verificationService;
    }

    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Kullanıcı adı oluşturma
            $usernameGenerator = new UsernameGenerator();
            $data['username'] = $usernameGenerator->generateRandomUsername($data['name']);

            // Kullanıcıyı oluştur
            $user = $this->authRepository->register($data);
            // Doğrulama e-postası gönder
            // Not: Burada $user bir array değil, User nesnesi olmalı
            $this->verificationService->resendVerificationEmail($user);

            return $user;
        });
    }


    public function login(array $credentials)
    {
        $user = User::where('email', $credentials['email'])->first();

        // Kullanıcı bulunamazsa veya şifre yanlışsa hata döndür
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return ['error' => 'Email or Password is incorrect'];
        }

        // E-posta doğrulama kontrolü
        if (!$user->hasVerifiedEmail()) {
            return ['error' => 'responses.auth.email_not_verified'];
        }

        // Kullanıcı doğrulanmışsa repository üzerinden giriş yaptır
        $loginResult = $this->authRepository->login($credentials);

        // Eğer başarılıysa ve token dönmüşse user bilgisi ile birleştir
        if (isset($loginResult['token'])) {
            // Kullanıcının giriş kaydını oluştur
            try {
                $user->recordLoginToday();
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Kullanıcı giriş kaydı oluşturulamadı: ' . $e->getMessage());
                // Hata durumunda login işlemine devam ediyoruz
            }
            
            // UserResource kullanarak kullanıcı bilgilerini zenginleştir
            $userResource = new \App\Http\Resources\Api\UserResource($user);
            $loginResult['user'] = $userResource;
        }

        return $loginResult;
    }

    public function logout()
    {
        return $this->authRepository->logout();
    }

    public function refresh()
    {
        return $this->authRepository->refresh();
    }

    public function forgotPassword(string $email)
    {
        // Kullanıcının olup olmadığını kontrol et
        $user = $this->authRepository->findByEmail($email);
        
        if (!$user) {
            return ['error' => 'Bu e-posta adresine sahip bir kullanıcı bulunamadı.'];
        }

        // Şifre sıfırlama bağlantısını gönder
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            return ['error' => 'Şifre sıfırlama bağlantısı gönderilemedi.'];
        }
        
        return ['message' => 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.'];
    }
}
