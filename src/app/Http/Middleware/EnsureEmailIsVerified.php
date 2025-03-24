<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponseTrait;
use App\Models\User;

class EnsureEmailIsVerified
{
    use ApiResponseTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->errorResponse('responses.verification.user_not_found', 404);
        }
        if (!$user->hasVerifiedEmail()) {
            return $this->errorResponse('responses.auth.email_not_verified', 403);
        }

        return $next($request);
    }
}
