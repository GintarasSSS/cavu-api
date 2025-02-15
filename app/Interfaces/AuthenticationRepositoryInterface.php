<?php

namespace App\Interfaces;

interface AuthenticationRepositoryInterface
{
    public function register(array $data): void;
    public function login(array $data): string;
    public function logout(): void;
}
