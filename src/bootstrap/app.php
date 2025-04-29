<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\JwtMiddleware;
use App\Traits\ApiResponseTrait;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Exceptions\UnauthorizedException;

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

    })->create();
