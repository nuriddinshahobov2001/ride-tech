<?php

namespace App\Repositories;

use App\Models\Car;
use App\Repositories\Interfaces\CarRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CarRepository implements CarRepositoryInterface
{
    public function __construct(
        private readonly Car $model
    ) {}

    public function getByUserId(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findById(int $id): ?Car
    {
        return $this->model->find($id);
    }

    public function create(array $data): Car
    {
        return $this->model->create($data);
    }

    public function update(Car $car, array $data): Car
    {
        $car->update($data);

        return $car->fresh();
    }

    public function delete(Car $car): bool
    {
        return (bool) $car->delete();
    }
}
