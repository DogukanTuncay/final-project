<?php

namespace App\Traits;
use App\Helpers\LocaleHelper;
trait ApiResponseTrait
{
    protected function successResponse($data = [], string $messageKey = null, int $status = 200, array $messageParams = [])
    {
        $locale = LocaleHelper::getUserLocale();
        $message = $messageKey ? __($messageKey, $messageParams, $locale) : 'Success';
        
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse(string $messageKey, int $status = 400, $errors = [], array $messageParams = [])
    {
        $locale = LocaleHelper::getUserLocale();
        $message = __($messageKey, $messageParams, $locale);
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
            'data' => null
        ], $status);
    }
}
