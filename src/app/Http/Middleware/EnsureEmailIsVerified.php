<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiResponseTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\User;

class EnsureEmailIsVerified
{
    use ApiResponseTrait;

    /**
     * Gelen isteği işle.
     *
     * Middleware'in `auth:api` (veya benzeri) bir kimlik doğrulama middleware'inden
     * sonra çalıştırıldığı varsayılır.
     *
     * @param  \\Illuminate\\Http\\Request  $request
     * @param  \\Closure(\\Illuminate\\Http\\Request): (\\Symfony\\Component\\HttpFoundation\\Response)  $next
     * @return \\Symfony\\Component\\HttpFoundation\\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Kullanıcı kimliği doğrulanmamışsa veya
        // kullanıcı MustVerifyEmail arayüzünü uyguluyor ve e-postası doğrulanmamışsa
        if (!$user ||
            ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()))
        {
            // Kimliği doğrulanmamışsa (genellikle auth middleware tarafından yakalanır ama ek kontrol)
            if (!$user) {
                return $this->errorResponse('responses.auth.unauthenticated', 401);
            }

            // E-posta doğrulanmamışsa
            return $this->errorResponse('responses.auth.email_not_verified', 403);
        }

        // Kullanıcının kimliği ve e-postası doğrulanmışsa, isteğin devam etmesine izin ver
        return $next($request);
    }
}
