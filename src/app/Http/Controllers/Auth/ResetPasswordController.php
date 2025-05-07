<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends BaseController
{
    /**
     * Şifre sıfırlama formunu göster
     *
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Şifre sıfırlamayı gerçekleştir
     *
     * @param \App\Http\Requests\Auth\ResetPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(ResetPasswordRequest $request)
    {
        // Şifre sıfırlama işlemini gerçekleştir
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        // Başarı durumuna göre yanıt döndür
        if ($status === Password::PASSWORD_RESET) {
            return redirect()->back()->with('success', 'Şifreniz başarıyla sıfırlandı. Şimdi giriş yapabilirsiniz.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => trans('passwords.'.$status)]);
    }
} 