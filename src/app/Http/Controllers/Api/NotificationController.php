<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\SendBroadcastNotificationRequest;
use App\Http\Requests\Api\SendCustomNotificationRequest;
use App\Interfaces\Services\Api\NotificationServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponseTrait;

    private NotificationServiceInterface $notificationService;

    public function __construct(NotificationServiceInterface $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Belirli kullanıcılara özel bildirim gönderir
     *
     * @param SendCustomNotificationRequest $request
     * @return JsonResponse
     */
    public function sendCustomNotification(SendCustomNotificationRequest $request): JsonResponse
    {
        $result = $this->notificationService->sendCustomNotification(
            $request->input('user_ids'),
            $request->input('title'),
            $request->input('message'),
            $request->input('additional_data', [])
        );

        if ($result) {
            return $this->successResponse(
                [],
                'notification.custom.success',
                200,
                ['count' => count((array) $request->input('user_ids'))]
            );
        }

        return $this->errorResponse('notification.custom.error', 500);
    }

    /**
     * Tüm kullanıcılara toplu bildirim gönderir
     *
     * @param SendBroadcastNotificationRequest $request
     * @return JsonResponse
     */
    public function sendBroadcastNotification(SendBroadcastNotificationRequest $request): JsonResponse
    {
        $result = $this->notificationService->sendBroadcastNotification(
            $request->input('title'),
            $request->input('message'),
            $request->input('additional_data', [])
        );

        if ($result) {
            return $this->successResponse([], 'notification.broadcast.success');
        }

        return $this->errorResponse('notification.broadcast.error', 500);
    }

    /**
     * Kullanıcının bildirim loglarını getirir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotificationLogs(Request $request): JsonResponse
    {
        try {
            $limit = $request->query('limit', 20);
            $user = $request->user();
            
            $logs = $user->notificationLogs()
                ->orderBy('sent_at', 'desc')
                ->paginate($limit);
            
            return $this->successResponse(
                $logs,
                'notification.logs.retrieved'
            );
        } catch (\Exception $e) {
            \Log::error('Bildirim logları getirilemedi: ' . $e->getMessage());
            return $this->errorResponse('notification.logs.error', 500);
        }
    }
    
    /**
     * Kullanıcının bildirim ayarlarını getirir
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getNotificationSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            return $this->successResponse(
                $user->notification_settings,
                'notification.settings.retrieved'
            );
        } catch (\Exception $e) {
            \Log::error('Bildirim ayarları getirilemedi: ' . $e->getMessage());
            return $this->errorResponse('notification.settings.error', 500);
        }
    }
    
    /**
     * Kullanıcının bildirim ayarlarını günceller
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function updateNotificationSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $settings = $request->input('settings');
            
            // Bildirim ayarlarını güncelle
            $user->updateNotificationSettings($settings);
            
            return $this->successResponse(
                $user->notification_settings,
                'notification.settings.updated'
            );
        } catch (\Exception $e) {
            \Log::error('Bildirim ayarları güncellenemedi: ' . $e->getMessage());
            return $this->errorResponse('notification.settings.update_error', 500);
        }
    }
} 