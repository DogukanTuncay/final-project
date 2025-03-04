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

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return isset($result['error'])
            ? $this->errorResponse($result['error'], 401)
            : $this->successResponse($result, 'User registered successfully');
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return isset($result['error'])
            ? $this->errorResponse($result['error'], 401)
            : $this->successResponse($result, 'User logged in successfully');
    }

    public function logout(): JsonResponse
    {
        return $this->successResponse($this->authService->logout(), 'User logged out successfully');
    }

    public function refresh(): JsonResponse
    {
        return $this->successResponse($this->authService->refresh(), 'Token refreshed successfully');
    }
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $this->authService->forgotPassword($request->email);
        return $this->successResponse([], 'Şifre sıfırlama bağlantısı e-posta adresinize gönderildi.');
    }

}
