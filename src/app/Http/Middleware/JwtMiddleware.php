<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;

class JwtMiddleware
{
    use ApiResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            try {
                $user = JWTAuth::parseToken()->authenticate();
            } catch (JWTException $e) {
                 return $this->errorResponse('errors.token_invalid', 401);
            }

            return $next($request);
        } catch (QueryException $e) {
            // TODO: Geçici çözüm - Exception Handler çalışmadığı için middleware'de yakalıyoruz
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            // PostgreSQL unique constraint ihlalleri (23505)
            if ($sqlState === '23505') {
                if (strpos($message, 'course_chapters_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_chapter_slug', 409);
                } 
                
                if (strpos($message, 'courses_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_slug', 409);
                }
                
                return $this->errorResponse('errors.duplicate_entry', 409);
            }
            
            // Diğer veritabanı hataları
            return $this->errorResponse('errors.database_error', 500);
        }
    }
}
