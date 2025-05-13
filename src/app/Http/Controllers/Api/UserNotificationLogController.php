<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserNotificationLogResource;
use App\Interfaces\Services\Api\UserNotificationLogServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserNotificationLogController extends Controller
{
    use ApiResponseTrait;

    protected UserNotificationLogServiceInterface $userNotificationLogService;

    /**
     * Controller sınıfının oluşturucusu
     *
     * @param UserNotificationLogServiceInterface $userNotificationLogService
     */
    public function __construct(UserNotificationLogServiceInterface $userNotificationLogService)
    {
        $this->userNotificationLogService = $userNotificationLogService;
    }

    /**
     * Kullanıcının bildirim günlüklerini listele
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'notification_type',
            'start_date',
            'end_date',
            'sort_field',
            'sort_direction',
        ]);

        $notifications = $this->userNotificationLogService->getCurrentUserNotifications($filters);
        
        return $this->successResponse(
            UserNotificationLogResource::collection($notifications),
            'notifications.list_success'
        );
    }

    /**
     * Belirli bir bildirim günlüğünü göster
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $notification = $this->userNotificationLogService->getNotificationById($id);
        
        if (!$notification) {
            return $this->errorResponse('notifications.not_found', 404);
        }
        
        return $this->successResponse(
            new UserNotificationLogResource($notification),
            'notifications.retrieve_success'
        );
    }

    /**
     * Belirli bir türdeki son bildirimi göster
     *
     * @param string $type
     * @return JsonResponse
     */
    public function lastOfType(string $type): JsonResponse
    {
        $notification = $this->userNotificationLogService->getLastNotificationOfType($type);
        
        if (!$notification) {
            return $this->errorResponse('notifications.not_found', 404);
        }
        
        return $this->successResponse(
            new UserNotificationLogResource($notification),
            'notifications.retrieve_success'
        );
    }

    /**
     * Belirli bir bildirim kaydını siler
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $result = $this->userNotificationLogService->deleteNotification($id);
        
        if (!$result) {
            return $this->errorResponse('notifications.delete_error', 404);
        }
        
        return $this->successResponse(null, 'notifications.delete_success');
    }

    /**
     * Kullanıcının tüm bildirim günlüklerini siler
     *
     * @return JsonResponse
     */
    public function destroyAll(): JsonResponse
    {
        $result = $this->userNotificationLogService->deleteAllNotifications();
        
        if (!$result) {
            return $this->errorResponse('notifications.delete_all_error', 500);
        }
        
        return $this->successResponse(null, 'notifications.delete_all_success');
    }
} 