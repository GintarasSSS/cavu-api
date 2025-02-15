<?php

namespace App\Repositories;

use App\Interfaces\AuthenticationRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthenticationRepository implements AuthenticationRepositoryInterface
{
    public function register(array $data): void
    {
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function login(array $data): string
    {
        if (!auth()->attempt($data)) {
            return '';
        }

        return auth()->user()->createToken($data['email'])->plainTextToken;
    }

    public function logout(): void
    {
        auth()->user()->tokens()->delete();
    }
}
