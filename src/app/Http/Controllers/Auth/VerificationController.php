<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Interfaces\Services\Auth\VerificationServiceInterface;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\VerifyEmailRequest;
use App\Http\Resources\Auth\VerificationResource;
use App\Models\User;
class VerificationController extends BaseController
{
    protected $verificationService;

    public function __construct(VerificationServiceInterface $verificationService)
    {
        $this->verificationService = $verificationService;
    }

    public function verify(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Invalid verification link or link has expired.'
            ], 400);
        }

        try {
            $result = $this->verificationService->verify($id, $request->hash);

            if ($result['verified']) {
                return response()->json([
                    'message' => 'Email verified successfully',
                    'user' => new VerificationResource($result['user'])
                ]);
            }

            return response()->json([
                'message' => 'Email already verified',
                'user' => new VerificationResource($result['user'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function resend(VerifyEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        try {
            $this->verificationService->resendVerificationEmail($user);

            return response()->json([
                'message' => 'Verification link sent to your email'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
