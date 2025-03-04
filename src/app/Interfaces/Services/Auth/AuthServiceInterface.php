<?php

namespace App\Interfaces\Services\Auth;

interface AuthServiceInterface
{
    public function register(array $data);
    public function login(array $credentials);
    public function logout();
    public function refresh();
    public function forgotPassword(string $email);
}
