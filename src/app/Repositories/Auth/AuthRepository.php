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
            'phone' => $data['phone'] ?? null,
            'zip_code' => $data['zip_code'] ?? null,
            'locale' => $data['locale'] ?? null,
            'password' => Hash::make($data['password']),
            'onesignal_player_id' => $data['onesignal_player_id'] ?? null,
        ]);


        return $user;
    }

    public function login(array $credentials)
    {
        // onesignal_player_id'i credentialsdan ayır, attempt için gereksiz
        $onesignalApiKey = $credentials['onesignal_player_id'] ?? null;
        $authCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password']
        ];

        if (!$token = JWTAuth::attempt($authCredentials)) {
            return ['error' => 'Email or Password is dismatch'];
        }
        
        $user = auth()->user();

        // Eğer istekte geçerli bir onesignal_player_id varsa ve kullanıcınınkinden farklıysa güncelle
        if (!is_null($onesignalApiKey) && $user->onesignal_player_id !== $onesignalApiKey) {
            $user->onesignal_player_id = $onesignalApiKey;
            $user->save();
            // Kullanıcı nesnesini yeniden yükleyerek güncel veriyi al (isteğe bağlı, token aynı kalır)
            // $user = $user->fresh(); 
        }

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
