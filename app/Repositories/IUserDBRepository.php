<?php

namespace App\Repositories;

interface IUserDBRepository
{
    public function create(array $data): int;

    public function update(int $id, array $data): bool;

    public function destroy(int $id): bool;

    public function find(int $id, array $relations = []): array;

    public function findByEmail(string $email): array;

    public function get(array $filter = []): array;
}
