<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface 
{
    public function __construct(
        private readonly User $model
    ) {}

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function findByPhone(string $phone): ?User
    {
        return $this->model->where('phone', $phone)->first();
    }

    public function findByEmailOrPhone(string $identifier): ?User
    {
        return $this->model->where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();
    }

    public function findById(int $id): ?User
    {
        return $this->model->find($id);
    }
}
