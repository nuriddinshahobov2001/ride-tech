<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;

    public function findByEmail(string $email): ?User;

    public function findByPhone(string $phone): ?User;

    public function findByEmailOrPhone(string $identifier): ?User;

    public function findById(int $id): ?User;
}
