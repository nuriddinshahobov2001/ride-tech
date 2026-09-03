<?php

use App\Helpers\ApiResponse;
use Illuminate\Http\JsonResponse;

if (! function_exists('api_response')) {
    function api_response(mixed $data = null, string $message = 'Успешно', int $status = 200): JsonResponse
    {
        return ApiResponse::success($data, $message, $status);
    }
}

if (! function_exists('api_error')) {
    function api_error(string $message = 'Произошла ошибка', int $status = 400, mixed $errors = null): JsonResponse
    {
        return ApiResponse::error($message, $status, $errors);
    }
}
