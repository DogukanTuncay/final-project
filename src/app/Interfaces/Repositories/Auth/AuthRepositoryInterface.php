<?php

namespace App\Interfaces\Repositories\Auth;

interface AuthRepositoryInterface
{
    public function register(array $data);
    public function login(array $credentials);
    public function logout();
    public function refresh();
    public function forgotPassword($email);
    public function findByEmail(string $email);
}
