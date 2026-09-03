<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $this->authService->register($request->validated());

        return api_response(
            data: $data,
            message: 'Регистрация прошла успешно.',
            status: 201
        );
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $login = $request->input('login') ?? $request->input('email');

        $data = $this->authService->login(
            login: (string) $login,
            password: (string) $request->input('password')
        );

        return api_response(
            data: $data,
            message: 'Вход успешно выполнен.'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return api_response(
            data: null,
            message: 'Токен успешно отозван. Выход выполнен.'
        );
    }
}
