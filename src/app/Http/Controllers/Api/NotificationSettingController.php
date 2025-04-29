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
    public function getSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $settings = UserNotificationSetting::firstOrCreate(
                ['user_id' => $user->id],
                ['preferences' => json_encode(UserNotificationSetting::getDefaultPreferences())]
            );

            return $this->successResponse(
                [
                    'preferences' => json_decode($settings->preferences, true)
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
    public function updateSettings(Request $request): JsonResponse
    {
        try {
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
            $settings = UserNotificationSetting::firstOrCreate(['user_id' => $user->id]);
            
            // Mevcut tercihleri al
            $currentPreferences = json_decode($settings->preferences, true) ?: UserNotificationSetting::getDefaultPreferences();
            
            // Yeni tercihlerle birleştir
            $preferences = array_merge($currentPreferences, $request->input('preferences'));
            
            // Tercihleri güncelle
            $settings->preferences = json_encode($preferences);
            $settings->save();

            return $this->successResponse(
                [
                    'preferences' => $preferences
                ],
                'notification.settings.updated'
            );
        } catch (\Exception $e) {
            Log::error('Bildirim ayarları güncellenemedi: ' . $e->getMessage());
            return $this->errorResponse('notification.settings.update_error', 500);
        }
    }

    /**
     * Kullanıcının bildirim ayarlarını varsayılana sıfırlar
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function resetSettings(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $settings = UserNotificationSetting::firstOrCreate(['user_id' => $user->id]);
            
            // Varsayılan tercihleri ayarla
            $settings->preferences = json_encode(UserNotificationSetting::getDefaultPreferences());
            $settings->save();

            return $this->successResponse(
                [
                    'preferences' => UserNotificationSetting::getDefaultPreferences()
                ],
                'notification.settings.reset'
            );
        } catch (\Exception $e) {
            Log::error('Bildirim ayarları sıfırlanamadı: ' . $e->getMessage());
            return $this->errorResponse('notification.settings.reset_error', 500);
        }
    }
} 