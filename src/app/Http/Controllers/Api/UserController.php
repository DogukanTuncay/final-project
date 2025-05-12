<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateUserLocaleRequest;
use App\Http\Requests\Api\UpdateUserProfileRequest;
use App\Interfaces\Services\Api\UserServiceInterface;
use App\Traits\ApiResponseTrait;
use App\Http\Resources\Api\UserLocaleResource;
use App\Http\Resources\Api\UserProfileResource;
use App\Http\Resources\Api\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Api\UpdateUserPasswordRequest;

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

            // Kimlik doğrulama kontrolü
            if (!$userId) {
                return $this->errorResponse('errors.unauthenticated', Response::HTTP_UNAUTHORIZED);
            }

            // Kullanıcıyı bul
            $user = $this->userService->getProfile($userId);
            if (!$user) {
                return $this->errorResponse('errors.user.profile_not_found', Response::HTTP_NOT_FOUND);
            }

            // Profil resmi yükleme işlemi
            if ($request->hasFile('profile_image')) {
                $user->uploadImage($request->file('profile_image'), 'profile_image');
                // Yüklenen resim HasImage trait'i tarafından kaydedildi, bu alanı validated dizisinden çıkar
                unset($validated['profile_image']);
            }

            // Güncellenecek alan yoksa hata vermek yerine mevcut profili döndür
            if (empty($validated)) {
                return $this->successResponse(
                    new UserProfileResource($user),
                    'messages.user.profile_no_changes'
                );
            }

            // Profil güncelleme işlemini gerçekleştir
            $updatedUser = $this->userService->updateProfile($userId, $validated);

            if (!$updatedUser) {
                return $this->errorResponse('errors.user.profile_update_failed', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Güncellenmiş profil bilgilerini döndür
            return $this->successResponse(
                new UserProfileResource($updatedUser),
                'messages.user.profile_updated'
            );

        } catch (\Exception $e) {
            Log::error('UserController updateProfile Error: ' . $e->getMessage(), [
                'user_id' => auth()->id() ?? 'unknown',
                'data' => $request->except(['password', 'profile_image'])
            ]);
            
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

    /**
     * Kullanıcının OneSignal player ID'sini günceller
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateOneSignalPlayerId(Request $request): JsonResponse
    {
        $request->validate([
            'player_id' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        $user->update([
            'onesignal_player_id' => $request->input('player_id'),
        ]);

        return $this->successResponse(
            [],
            'user.onesignal_updated'
        );
    }

    /**
     * Kullanıcı şifresini değiştirir
     *
     * @param UpdateUserPasswordRequest $request
     * @return JsonResponse
     */
    public function updatePassword(UpdateUserPasswordRequest $request): JsonResponse
    {
        $userId = auth()->id();
        $validated = $request->validated();
        $result = $this->userService->updatePassword(
            $userId,
            $validated['current_password'],
            $validated['password']
        );
        if ($result === true) {
            return $this->successResponse([], 'Şifreniz başarıyla değiştirildi.');
        }
        return $this->errorResponse($result, 400);
    }

    // Gelecekte diğer kullanıcı işlemleri buraya eklenebilir (örn: get profile)
} 