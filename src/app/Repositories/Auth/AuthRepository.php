<?php

namespace App\Repositories\Auth;

use App\Interfaces\Repositories\Auth\AuthRepositoryInterface;
use App\Models\User;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
class AuthRepository extends BaseRepository implements AuthRepositoryInterface
{
    public function __construct()
    {
        // Burada User modelini inject ediyoruz
        parent::__construct(new User());
    }
    public function register(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'zip_code' => $data['zip_code'],
            'locale' => $data['locale'],
            'password' => Hash::make($data['password']),
        ]);

        $user->token = JWTAuth::fromUser($user);

        return $user;
    }

    public function login(array $credentials)
    {

        if (!$token = JWTAuth::attempt($credentials)) {
            return ['error' => 'Email or Password is dismatch'];
        }
        $user = auth()->user();
        return compact('token', 'user');
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return null;
    }

    public function refresh()
    {
        return ['token' => JWTAuth::refresh(JWTAuth::getToken())];
    }
    public function forgotPassword($request) {
       return Password::sendResetLink($request->only('email'));

    }
    public function findByEmail(string $email)
{
    return User::where('email', $email)->first();
}
}
