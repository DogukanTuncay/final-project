<?php

namespace App\Traits;

trait ApiResponseTrait
{
    protected function successResponse($data = [], string $messageKey = null, int $status = 200, array $messageParams = [])
    {
        $message = $messageKey ? __($messageKey, $messageParams) : 'Success';
        
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function errorResponse(string $messageKey, int $status = 400, $errors = [], array $messageParams = [])
    {
        $message = __($messageKey, $messageParams);
        
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }
}
