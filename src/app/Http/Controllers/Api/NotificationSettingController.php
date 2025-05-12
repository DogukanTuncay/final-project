<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserNotificationSetting;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationSettingController extends Controller
{
    use ApiResponseTrait;

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
            $settings = $user->notificationSettings()->firstOrCreate([
                'user_id' => $user->id
            ], [
                'preferences' => UserNotificationSetting::getDefaultPreferences()
            ]);

            $preferences = $settings->preferences;
            if (is_string($preferences)) {
                $preferences = json_decode($preferences, true) ?: UserNotificationSetting::getDefaultPreferences();
            }

            Log::info('Bildirim ayarları alınıyor', ['preferences' => $preferences]);

            return $this->successResponse(
                [
                    'preferences' => $preferences
                ],
                'notification.settings.retrieved'
            );
        } catch (\Exception $e) {
            Log::error('Bildirim ayarları getirilemedi: ' . $e->getMessage());
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
            Log::info('Bildirim ayarları güncelleme isteği alındı', ['request_body' => $request->all()]);
            
            $request->validate([
                'preferences' => 'required|array',
                'preferences.all' => 'boolean',
                'preferences.login_streak' => 'boolean',
                'preferences.course_completion' => 'boolean',
                'preferences.course_reminder' => 'boolean',
                'preferences.custom' => 'boolean',
                'preferences.broadcast' => 'boolean',
            ]);

            $user = $request->user();
            Log::info('Kullanıcı bilgileri', ['user_id' => $user->id, 'email' => $user->email]);
            
            // Doğrudan gelen tercihleri güncelle
            $preferences = $request->input('preferences');
            $user->updateNotificationSettings($preferences);
            
            // Güncellenmiş ayarları al
            $updatedSettings = $user->fresh()->notificationSettings()->first();
            $updatedPreferences = $updatedSettings->preferences;
            if (is_string($updatedPreferences)) {
                $updatedPreferences = json_decode($updatedPreferences, true) ?: [];
            }
            
            Log::info('Güncellenmiş bildirim ayarları', ['updated_preferences' => $updatedPreferences]);

            return $this->successResponse(
                [
                    'preferences' => $updatedPreferences
                ],
                'notification.settings.updated'
            );
        } catch (\Exception $e) {
            Log::error('Bildirim ayarları güncellenemedi: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
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
            $defaultPreferences = UserNotificationSetting::getDefaultPreferences();
            
            return $this->successResponse(
                [
                    'preferences' => $defaultPreferences
                ],
                'notification.settings.defaults'
            );
        } catch (\Exception $e) {
            Log::error('Varsayılan bildirim ayarları getirilemedi: ' . $e->getMessage());
            return $this->errorResponse('notification.settings.default_error', 500);
        }
    }
} 