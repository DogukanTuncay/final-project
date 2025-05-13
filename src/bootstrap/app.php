<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\JwtMiddleware;
use App\Traits\ApiResponseTrait;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Illuminate\Database\QueryException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
            'JWT' => JwtMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'record.login' => \App\Http\Middleware\RecordUserLogin::class,
            'check.app.version' => \App\Http\Middleware\CheckMobileAppVersion::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $apiResponder = new class { use ApiResponseTrait; };

        $exceptions->renderable(function (UnauthorizedException $e, $request) use ($apiResponder) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                $message = 'Bu işlemi yapmak için gerekli yetkiye sahip değilsiniz.';
                
                if (str_contains($e->getMessage(), 'necessary roles')) {
                    $message = 'Bu işlemi yapmak için gerekli role sahip değilsiniz.';
                } elseif (str_contains($e->getMessage(), 'necessary permissions')) {
                     $message = 'Bu işlemi yapmak için gerekli izne sahip değilsiniz.';
                } elseif ($e->getMessage() !== 'User does not have the right roles.' && $e->getMessage() !== 'User does not have the right permissions.'){
                     $message = $e->getMessage();
                }

                return $apiResponder->errorResponse(
                    $message,
                    Response::HTTP_FORBIDDEN
                );
            }
        });
        
        $exceptions->renderable(function (\Illuminate\Validation\ValidationException $e, $request) use ($apiResponder){
            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                return $apiResponder->errorResponse(
                    __('errors.validation_error'), 
                    Response::HTTP_UNPROCESSABLE_ENTITY, 
                    $e->errors()
                );
            }
        });

        $exceptions->renderable(function (\Illuminate\Auth\AuthenticationException $e, $request) use ($apiResponder){
             if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                return $apiResponder->errorResponse(__('errors.unauthenticated'), Response::HTTP_UNAUTHORIZED);
             }
        });
        
         $exceptions->renderable(function (\Illuminate\Auth\Access\AuthorizationException $e, $request) use ($apiResponder){
             if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                return $apiResponder->errorResponse(__('errors.forbidden'), Response::HTTP_FORBIDDEN);
             }
        });

        $exceptions->renderable(function (\Illuminate\Database\Eloquent\ModelNotFoundException $e, $request) use ($apiResponder){
             if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                $model = strtolower(class_basename($e->getModel()));
                return $apiResponder->errorResponse(
                    __('errors.not_found', ['model' => $model]), 
                    Response::HTTP_NOT_FOUND
                );
             }
        });

        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) use ($apiResponder){
             if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                return $apiResponder->errorResponse(__('errors.route_not_found'), Response::HTTP_NOT_FOUND);
             }
        });
        
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, $request) use ($apiResponder){
             if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                return $apiResponder->errorResponse(__('errors.method_not_allowed'), Response::HTTP_METHOD_NOT_ALLOWED);
             }
        });

        // SQL hatalarını ele alma
        $exceptions->renderable(function (QueryException $e, $request) use ($apiResponder) {
            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin/*')) {
                $errorCode = $e->getCode();
                $message = 'Veritabanı işlemi sırasında bir hata oluştu.';
                $status = Response::HTTP_INTERNAL_SERVER_ERROR;
                $debug = [];
                
                // Hata mesajını ve durumunu belirle
                if (app()->environment('local', 'development', 'testing')) {
                    $debug = [
                        'sql' => $e->getSql() ?? null,
                        'bindings' => $e->getBindings() ?? [],
                        'code' => $errorCode,
                        'message' => $e->getMessage()
                    ];
                }

                // Yaygın SQL hata kodlarını kontrol et ve kullanıcı dostu mesajlar göster
                switch ($errorCode) {
                    case '23000': // Bütünlük kısıtlaması ihlali
                        if (stripos($e->getMessage(), 'foreign key constraint fails') !== false) {
                            $message = 'Bu kaydı silemezsiniz çünkü diğer kayıtlar tarafından kullanılıyor.';
                            $status = Response::HTTP_CONFLICT;
                        } elseif (stripos($e->getMessage(), 'duplicate') !== false || stripos($e->getMessage(), 'unique') !== false) {
                            $message = 'Bu kayıt zaten mevcut. Lütfen benzersiz bir değer girin.';
                            $status = Response::HTTP_CONFLICT;
                        }
                        break;
                    case '42S02': // Tablo bulunamadı
                        $message = 'Sistem veritabanı yapılandırmasında bir hata oluştu.';
                        break;
                    case '42S22': // Sütun bulunamadı
                        $message = 'Sistem veritabanı alanlarında bir hata oluştu.';
                        break;
                    case '42000': // Sözdizimi hatası
                        $message = 'Veritabanı sorgusu sözdizimi hatası.';
                        break;
                    case 'HY000': // Genel hata
                        if (stripos($e->getMessage(), 'too many connections') !== false) {
                            $message = 'Sistem şu anda çok yoğun, lütfen daha sonra tekrar deneyin.';
                            $status = Response::HTTP_SERVICE_UNAVAILABLE;
                        }
                        break;
                }

                // Settings güncellemesi için özel kontrol
                if (stripos($request->path(), 'settings') !== false && stripos($e->getMessage(), 'value') !== false) {
                    $message = 'Ayar değeri uygun formatta değil. Lütfen geçerli bir değer girin.';
                    $status = Response::HTTP_UNPROCESSABLE_ENTITY;
                }

                return $apiResponder->errorResponse($message, $status, $debug);
            }
        });

    })->create();
