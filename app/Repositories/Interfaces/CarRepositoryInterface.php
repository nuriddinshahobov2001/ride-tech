<?php

namespace App\Repositories\Interfaces;

use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;

interface CarRepositoryInterface
{
    public function getByUserId(int $userId): Collection;

    public function findById(int $id): ?Car;

    public function create(array $data): Car;

    public function update(Car $car, array $data): Car;

    public function delete(Car $car): bool;
}
