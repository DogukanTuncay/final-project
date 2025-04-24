<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Interfaces\Services\Auth\AuthServiceInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends BaseController
{
    protected $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Kullanıcı kaydı
     * 
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        return isset($result['error'])
            ? $this->errorResponse($result['error'], 401)
            : $this->successResponse($result, 'responses.auth.register_success');
    }

    /**
     * Kullanıcı girişi
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 401);
        }

        // UserResource'un içeriğini JSON'a dönüştür, direkt olarak UserResource nesnesi dönmemesi için
        if (isset($result['user']) && $result['user'] instanceof \Illuminate\Http\Resources\Json\JsonResource) {
            $result['user'] = $result['user']->resolve(request());
        }
        return $this->successResponse($result, 'responses.auth.login_success');
    }

    /**
     * Kullanıcı çıkışı
     * 
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $result = $this->authService->logout();
        return $this->successResponse($result, 'responses.auth.logout_success');
    }

    /**
     * Token yenileme
     * 
     * @return JsonResponse
     */
    public function refresh(): JsonResponse
    {
        $result = $this->authService->refresh();
        return $this->successResponse($result, 'responses.auth.refresh_success');
    }

    /**
     * Şifre sıfırlama emaili gönder
     * 
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $result = $this->authService->forgotPassword($request->email);
        
        if (isset($result['error'])) {
            return $this->errorResponse($result['error'], 401);
        }
        
        return $this->successResponse(null, $result['message'] ?? 'responses.auth.forgot_password_success');
    }

    /**
     * Kullanıcı profil bilgilerini getir
     * 
     * @return JsonResponse
     */
    public function me(): JsonResponse
    {
        $user = auth()->user();
        
        if (!$user) {
            return $this->errorResponse('errors.auth.unauthorized', 401);
        }
        
        // UserResource kullanarak seviye bilgilerini de içeren zengin veri dön
        $userResource = new \App\Http\Resources\Api\UserResource($user);
        
        return $this->successResponse($userResource, 'responses.auth.profile_success');
    }
}
