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
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Exceptions\UniqueConstraintException;

class Handler extends ExceptionHandler
{
    use ApiResponseTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        // 
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Özel UniqueConstraintException işleme
        $this->renderable(function (UniqueConstraintException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                $constraint = $e->getConstraint();
                
                if ($e->isCourseChapterSlugViolation()) {
                    return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT);
                } 
                
                if ($e->isCourseSlugViolation()) {
                    return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
                }
                
                return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
            }
            return null;
        });

        // UniqueConstraintViolationException için özel handling
        $this->renderable(function (UniqueConstraintViolationException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                $message = $e->getMessage();
                
                // Özel constraint mesajları
                if (strpos($message, 'course_chapters_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT);
                } 
                elseif (strpos($message, 'courses_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
                }
                
                return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
            }
            return null;
        });

        // QueryException için özel handling - UniqueConstraint veya PostgreSQL hatalarını yakala
        $this->renderable(function (QueryException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                $sqlState = $e->errorInfo[0] ?? null;
                $message = $e->getMessage();
                
                // PostgreSQL unique constraint ihlalleri (23505)
                if ($sqlState === '23505') {
                    // Özel constraint mesajları
                    if (strpos($message, 'course_chapters_slug_unique') !== false) {
                        return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT);
                    } 
                    elseif (strpos($message, 'courses_slug_unique') !== false) {
                        return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
                    }
                    
                    return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
                }
                
                // MySQL unique constraint violation (1062)
                $errorCode = $e->errorInfo[1] ?? null;
                if ($errorCode == 1062) {
                    return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
                }
                
                return $this->errorResponse('errors.database_error', Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return null;
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $exception)
    {
        // Sadece API istekleri için özel hata yanıtları
        if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
            
            // QueryException işleme - PostgreSQL unique constraint ihlalleri
            if ($exception instanceof QueryException) {
                $message = $exception->getMessage();
                $sqlState = $exception->errorInfo[0] ?? null;
            
                // Debug amacıyla loglama
                Log::debug('Database Exception:', [
                    'message' => $message,
                    'sql_state' => $sqlState,
                    'error_code' => $exception->errorInfo[1] ?? null
                ]);
                
                // PostgreSQL unique constraint ihlali (23505)
                if ($sqlState === '23505') {
                    // Hangi constraint'in ihlal edildiğini anlama
                    if (strpos($message, 'course_chapters_slug_unique') !== false) {
                        return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT);
                    } 
                    
                    if (strpos($message, 'courses_slug_unique') !== false) {
                        return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
                    }
                    
                    // Genel unique constraint ihlali
                    return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
                }
                
                // MySQL unique constraint ihlali (1062)
                $errorCode = $exception->errorInfo[1] ?? null;
                if ($errorCode == 1062) {
                    return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
                }
                
                // Diğer veritabanı hataları
                return $this->errorResponse(
                    'errors.database_error', 
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }
            
            // UniqueConstraintViolationException - Illuminate'in kendi unique constraint exception'ı
            if ($exception instanceof UniqueConstraintViolationException) {
                $message = $exception->getMessage();
                
                // Spesifik constraint mesajları
                if (strpos($message, 'course_chapters_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT);
                } 
                
                if (strpos($message, 'courses_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
                }
                
                return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
            }
            
            // Doğrulama hataları
            if ($exception instanceof ValidationException) {
                return $this->errorResponse(
                    'errors.validation_error', 
                    Response::HTTP_UNPROCESSABLE_ENTITY, 
                    $exception->errors()
                );
            }
            
            // Model bulunamadı hataları
            if ($exception instanceof ModelNotFoundException) {
                $model = strtolower(class_basename($exception->getModel()));
                return $this->errorResponse(
                    'errors.not_found', 
                    Response::HTTP_NOT_FOUND, 
                    ['model' => $model]
                );
            }
            
            // Route bulunamadı hataları
            if ($exception instanceof NotFoundHttpException) {
                return $this->errorResponse('errors.route_not_found', Response::HTTP_NOT_FOUND);
            }
            
            // Metod izin verilmedi hataları
            if ($exception instanceof MethodNotAllowedHttpException) {
                return $this->errorResponse('errors.method_not_allowed', Response::HTTP_METHOD_NOT_ALLOWED);
            }
            
            // Yetki hataları
            if ($exception instanceof AuthorizationException) {
                return $this->errorResponse('errors.forbidden', Response::HTTP_FORBIDDEN);
            }
            
            // Kimlik doğrulama hataları
            if ($exception instanceof AuthenticationException) {
                return $this->errorResponse('errors.unauthenticated', Response::HTTP_UNAUTHORIZED);
            }
            
            // HTTP exception sınıfı
            if ($exception instanceof HttpException) {
                return $this->errorResponse(
                    'errors.http_error', 
                    $exception->getStatusCode()
                );
            }
            
            // Bilinmeyen diğer hata türleri
            Log::error('Unhandled Exception:', [
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
            
            // Geliştirme ortamında detaylı, üretimde genel hata
            $errorMessage = app()->environment('production') 
                ? 'errors.server_error' 
                : $exception->getMessage();
                
            return $this->errorResponse(
                $errorMessage, 
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
        
        // Web istekleri için Laravel'in varsayılan davranışını kullan
        return parent::render($request, $exception);
    }

    /**
     * Hataları raporla
     */
    public function report(Throwable $exception)
    {
        // Laravel'in kendi hata raporlama sistemini çalıştır
        parent::report($exception);

        // Hataları log dosyasına kaydet
        Log::error($exception->getMessage(), [
            'exception' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);
    }
}
