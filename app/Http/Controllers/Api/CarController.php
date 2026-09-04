<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Services\CarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarController extends Controller
{
    public function __construct(
        private readonly CarService $carService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cars = $this->carService->getDriverCars($request->user()->id);

        return api_response($cars, 'Список автомобилей успешно получен.');
    }

    public function store(StoreCarRequest $request): JsonResponse
    {
        $car = $this->carService->createCar(
            userId: $request->user()->id,
            data: $request->validated()
        );

        return api_response($car, 'Автомобиль успешно добавлен.', 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        try {
            $car = $this->carService->getCarById($request->user()->id, $id);

            return api_response($car, 'Информация об автомобиле успешно получена.');
        } catch (NotFoundHttpException $e) {
            return api_error($e->getMessage(), 404);
        } catch (AccessDeniedHttpException $e) {
            return api_error($e->getMessage(), 403);
        }
    }

    public function update(UpdateCarRequest $request, int $id): JsonResponse
    {
        try {
            $car = $this->carService->updateCar(
                userId: $request->user()->id,
                carId: $id,
                data: $request->validated()
            );

            return api_response($car, 'Информация об автомобиле успешно обновлена.');
        } catch (NotFoundHttpException $e) {
            return api_error($e->getMessage(), 404);
        } catch (AccessDeniedHttpException $e) {
            return api_error($e->getMessage(), 403);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        try {
            $this->carService->deleteCar($request->user()->id, $id);

            return api_response(null, 'Автомобиль успешно удален.');
        } catch (NotFoundHttpException $e) {
            return api_error($e->getMessage(), 404);
        } catch (AccessDeniedHttpException $e) {
            return api_error($e->getMessage(), 403);
        }
    }
}
