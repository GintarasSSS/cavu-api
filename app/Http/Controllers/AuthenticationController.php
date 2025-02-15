<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostLoginRequest;
use App\Http\Requests\PostRegisterRequest;
use App\Interfaces\AuthenticationRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function __construct(private readonly AuthenticationRepositoryInterface $authRepository)
    {
    }

    public function register(PostRegisterRequest $request): JsonResponse
    {
        $this->authRepository->register($request->validated());

        return response()->json(
            ['status' => 'success'],
            Response::HTTP_CREATED
        );
    }

    public function login(PostLoginRequest $request): JsonResponse
    {
        if (!$token = $this->authRepository->login($request->validated())) {
            return response()->json(
                ['status' => 'failed'],
                Response::HTTP_UNAUTHORIZED
            );
        }

        return response()->json([
            'status' => 'success',
            'token' => $token
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->authRepository->logout();

        return response()->json(['status' => 'success']);
    }
}
