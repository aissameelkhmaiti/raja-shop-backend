<?php

namespace App\Services\Interfaces;

use App\Models\User;

interface UserServiceInterface
{
    public function register(array $data): User;

    public function login(array $data): ?array;

    public function logout(User $user): void;
}
