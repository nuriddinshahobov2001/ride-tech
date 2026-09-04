<?php

namespace App\Services;

use App\Models\Car;
use App\Repositories\Interfaces\CarRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CarService
{
    public function __construct(
        private readonly CarRepositoryInterface $carRepository
    ) {}

    public function getDriverCars(int $userId): Collection
    {
        return $this->carRepository->getByUserId($userId);
    }

    public function createCar(int $userId, array $data): Car
    {
        $data['user_id'] = $userId;

        return $this->carRepository->create($data);
    }

    public function getCarById(int $userId, int $carId): Car
    {
        $car = $this->carRepository->findById($carId);

        if (! $car) {
            throw new NotFoundHttpException('Автомобиль не найден.');
        }

        if ($car->user_id !== $userId) {
            throw new AccessDeniedHttpException('Вы не являетесь владельцем этого автомобиля.');
        }

        return $car;
    }

    public function updateCar(int $userId, int $carId, array $data): Car
    {
        $car = $this->getCarById($userId, $carId);

        return $this->carRepository->update($car, $data);
    }

    public function deleteCar(int $userId, int $carId): void
    {
        $car = $this->getCarById($userId, $carId);

        $this->carRepository->delete($car);
    }
}
