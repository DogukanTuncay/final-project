<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class CatchDatabaseErrors
{
    use ApiResponseTrait;
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (QueryException $e) {
            // TODO: Daha kalıcı bir çözüm bulunana kadar geçici olarak burada hataları yakalayalım
            Log::debug('Database Error Caught in Middleware:', [
                'message' => $e->getMessage(),
                'sql_state' => $e->errorInfo[0] ?? null,
                'error_code' => $e->errorInfo[1] ?? null
            ]);
            
            $message = $e->getMessage();
            $sqlState = $e->errorInfo[0] ?? null;
            
            // PostgreSQL unique constraint ihlalleri (23505)
            if ($sqlState === '23505') {
                if (strpos($message, 'course_chapters_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_chapter_slug', Response::HTTP_CONFLICT);
                } 
                
                if (strpos($message, 'courses_slug_unique') !== false) {
                    return $this->errorResponse('errors.duplicate_course_slug', Response::HTTP_CONFLICT);
                }
                
                return $this->errorResponse('errors.duplicate_entry', Response::HTTP_CONFLICT);
            }
            
            return $this->errorResponse('errors.database_error', Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
} 