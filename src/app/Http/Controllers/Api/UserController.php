<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserLocaleRequest;
use App\Http\Requests\Api\UpdateUserProfileRequest;
use App\Interfaces\Services\Api\UserServiceInterface;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\Api\UserLocaleResource;
use App\Http\Resources\Api\UserProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userService;

    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get the authenticated user's profile information.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function profile(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id; // veya auth()->id();
            $user = $this->userService->getProfile($userId);

            if (!$user) {
                // Kullanıcı bir şekilde bulunamazsa (nadiren olmalı)
                return $this->errorResponse('errors.user.profile_not_found', Response::HTTP_NOT_FOUND);
            }

            return $this->successResponse(
                new UserProfileResource($user),
                'messages.user.profile_retrieved'
            );

        } catch (\Exception $e) {
            Log::error('UserController profile Error: ' . $e->getMessage());
            return $this->errorResponse('errors.general_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the authenticated user's profile information.
     *
     * @param UpdateUserProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateUserProfileRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $userId = auth()->id();

            if (!$userId) {
                return $this->errorResponse('errors.unauthenticated', Response::HTTP_UNAUTHORIZED);
            }

            $updatedUser = $this->userService->updateProfile($userId, $validated);

            if (!$updatedUser) {
                return $this->errorResponse('errors.user.profile_update_failed', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->successResponse(
                new UserProfileResource($updatedUser), // Güncellenmiş tam profili döndür
                'messages.user.profile_updated'
            );

        } catch (\Exception $e) {
            Log::error('UserController updateProfile Error: ' . $e->getMessage());
            return $this->errorResponse('errors.general_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the authenticated user's locale.
     *
     * @param UpdateUserLocaleRequest $request
     * @return JsonResponse
     */
    public function updateLocale(UpdateUserLocaleRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $userId = auth()->id(); // Authenticated user ID

            if (!$userId) {
                // Bu durum normalde middleware tarafından yakalanmalı ama yine de kontrol edelim
                return $this->errorResponse('errors.unauthenticated', Response::HTTP_UNAUTHORIZED);
            }

            $updatedUser = $this->userService->updateLocale($userId, $validated['locale']);

            if (!$updatedUser) {
                // Service null dönerse (örn. kullanıcı bulunamadı veya db hatası)
                return $this->errorResponse('errors.user.locale_update_failed', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return $this->successResponse(
                new UserLocaleResource($updatedUser),
                'messages.user.locale_updated'
            );

        } catch (\Exception $e) {
            Log::error('UserController updateLocale Error: ' . $e->getMessage());
            return $this->errorResponse('errors.general_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // Gelecekte diğer kullanıcı işlemleri buraya eklenebilir (örn: get profile)
} 