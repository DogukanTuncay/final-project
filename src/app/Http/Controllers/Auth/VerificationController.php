<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Auth\VerificationServiceInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\Auth\VerificationResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class VerificationController extends BaseController
{
    protected $verificationService;

    public function __construct(VerificationServiceInterface $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    /**
     * Email adresini doğrula
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse|\Illuminate\Http\Response
     */
    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            if ($request->wantsJson()) {
                return $this->errorResponse('responses.verification.invalid_link', 400);
            }
            
            return abort(403, __('responses.verification.invalid_link'));
        }

        try {
            $result = $this->verificationService->verify($id, $request->hash);
            
            if ($request->wantsJson()) {
                if ($result['verified']) {
                    return $this->successResponse(
                        ['user' => new VerificationResource($result['user'])],
                        'responses.verification.success'
                    );
                }

                return $this->successResponse(
                    ['user' => new VerificationResource($result['user'])],
                    'responses.verification.already_verified'
                );
            }
            
            // Web isteği için görünüm döndür
            return view('auth.email-verified', [
                'user' => $result['user'],
                'verified' => $result['verified']
            ]);
            
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return $this->errorResponse($e->getMessage(), 400);
            }
            
            return abort(400, $e->getMessage());
        }
    }

    /**
     * Doğrulama emailini tekrar gönder
     *
     * @param VerifyEmailRequest $request
     * @return JsonResponse
     */
    public function resend(VerifyEmailRequest $request): JsonResponse
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->errorResponse('responses.verification.user_not_found', 404);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->errorResponse('responses.verification.already_verified', 400);
        }

        try {
            $this->verificationService->resendVerificationEmail($user);
            return $this->successResponse(null, 'responses.verification.link_sent');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }
}
