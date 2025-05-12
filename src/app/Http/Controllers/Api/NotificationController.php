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
            $settings = $user->notificationSettings()->firstOrCreate(
                ['user_id' => $user->id],
                ['preferences' => \App\Models\UserNotificationSetting::getDefaultPreferences()]
            );
            
            return $this->successResponse(
                ['preferences' => $settings->preferences],
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
            $request->validate([
                'preferences' => 'required|array',
                'preferences.all_notifications' => 'array',
                'preferences.all_notifications.enabled' => 'boolean',
                'preferences.login_series' => 'array',
                'preferences.login_series.enabled' => 'boolean',
                'preferences.course_reminders' => 'array',
                'preferences.course_reminders.enabled' => 'boolean',
                'preferences.special_notifications' => 'array',
                'preferences.special_notifications.enabled' => 'boolean',
                'preferences.announcements' => 'array',
                'preferences.announcements.enabled' => 'boolean',
            ]);
            
            $user = $request->user();
            $preferences = $request->input('preferences');
            
            // Bildirim ayarlarını güncelle
            $user->updateNotificationSettings($preferences);
            
            $settings = $user->notificationSettings;
            
            return $this->successResponse(
                ['preferences' => $settings->preferences],
                'notification.settings.updated'
            );
        } catch (\Exception $e) {
            \Log::error('Bildirim ayarları güncellenemedi: ' . $e->getMessage());
            return $this->errorResponse('notification.settings.update_error', 500);
        }
    }

    /**
     * Varsayılan bildirim ayarlarını getirir
     * 
     * @return JsonResponse
     */
    public function getDefaultSettings(): JsonResponse
    {
        try {
            $defaultPreferences = \App\Models\UserNotificationSetting::getDefaultPreferences();
            
            return $this->successResponse(
                ['preferences' => $defaultPreferences],
                'notification.settings.defaults_retrieved'
            );
        } catch (\Exception $e) {
            \Log::error('Varsayılan bildirim ayarları getirilemedi: ' . $e->getMessage());
            return $this->errorResponse('notification.settings.defaults_error', 500);
        }
    }

    /**
     * Tüm bildirim kontrollerini yapıp sonuçları döndürür
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAllNotifications(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $results = $this->notificationService->checkAllNotifications($user);
        
        return $this->successResponse($results, 'notifications.check_completed');
    }

    /**
     * Kullanıcının login streak bildirimi kontrolünü yapar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkLoginStreakNotification(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $result = $this->notificationService->checkAndSendLoginStreakNotification($user);
        
        if ($result) {
            return $this->successResponse(['sent' => true], 'notifications.login_streak_sent');
        }
        
        return $this->successResponse(['sent' => false], 'notifications.no_login_streak_notification');
    }

    /**
     * Kullanıcının kurs hatırlatıcı bildirimi kontrolünü yapar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkCourseReminderNotification(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $result = $this->notificationService->checkAndSendCourseReminderNotification($user);
        
        if ($result) {
            return $this->successResponse(['sent' => true], 'notifications.course_reminder_sent');
        }
        
        return $this->successResponse(['sent' => false], 'notifications.no_course_reminder_notification');
    }
} 