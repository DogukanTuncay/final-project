<?php

namespace App\Traits;

use App\Services\Api\EventService;
use App\Helpers\LocaleHelper;

trait ApiResponseTrait
{
    public function successResponse($data = [], string $messageKey = null, int $status = 200, array $messageParams = [])
    {
        $locale = LocaleHelper::getUserLocale();
        $message = $messageKey ? __($messageKey, $messageParams, $locale) : 'Success';

        // EventService'den eventleri al
        $eventService = app(EventService::class);
        $events = $eventService->getEvents();
        $eventService->clearEvents();

        // Data'yı hazırla
        $responseData = [
            'events' => $events
        ];

        // Eğer data varsa ekle
        if (!empty($data)) {
            $responseData = array_merge(is_array($data) ? $data : ['data' => $data], $responseData);
        }

        return response()->json([
            'status' => 'success',
            'message' => $message,
            'errors' => (object)[], // Her zaman {} dönecek
            'data' => $responseData
        ], $status);
    }

    public function errorResponse(string $messageKey, int $status = 400, $errors = [], array $messageParams = [])
    {
        $locale = LocaleHelper::getUserLocale();
        $message = __($messageKey, $messageParams, $locale);

        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'data' => [
                'events' => [] // Hata durumunda boş events array'i
            ]
        ], $status);
    }
}
