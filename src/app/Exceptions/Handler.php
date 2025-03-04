<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponseTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    public function register()
    {
        $this->renderable(function (Throwable $e, $request) {
            return $this->handleError($e);
        });
    }

    /**
     * Hata türüne göre uygun errorResponse döndür.
     */
    protected function handleError(Throwable $e)
    {
        // Doğrulama hatası
        if ($e instanceof ValidationException) {
            return $this->errorResponse(json_encode($e->errors()), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        // SMTP Hata Yönetimi (Mailer TransportException)
    if ($e instanceof TransportException) {
        return $this->errorResponse('E-posta gönderilirken bir hata oluştu. Lütfen SMTP ayarlarınızı kontrol edin.', 500);
    }
        // Model bulunamadı hatası
        if ($e instanceof ModelNotFoundException) {
            return $this->errorResponse('Kayıt bulunamadı.', Response::HTTP_NOT_FOUND);
        }

        // Yetkilendirme hatası
        if ($e instanceof AuthorizationException) {
            return $this->errorResponse('Bu işlemi yapmak için yetkiniz yok.', Response::HTTP_FORBIDDEN);
        }

        // Kimlik doğrulama hatası
        if ($e instanceof AuthenticationException) {
            return $this->errorResponse('Giriş yapmanız gerekiyor.', Response::HTTP_UNAUTHORIZED);
        }

        // Tüm diğer hata türleri
        return $this->errorResponse(
            env('APP_DEBUG') ? $e->getMessage() : 'Beklenmedik bir hata oluştu.',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }

    public function report(Throwable $exception)
{
    // Laravel'in kendi hata raporlama sistemini çalıştır
    parent::report($exception);

    // Hataları `storage/logs/laravel.log` içine kaydet
    Log::error($exception->getMessage(), [
        'exception' => $exception,
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
    ]);
}


}
