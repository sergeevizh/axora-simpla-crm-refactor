<?php

namespace App\Eloquent\Queries;

use App\Eloquent\Models\User;
use App\Repositories\IUserDBRepository;

class EloquentUserQueries implements IUserDBRepository
{
    public function create(array $data): int
    {
        $user = User::create($data);

        return $user ? $user->id : '';
    }

    public function update(int $id, array $data): bool
    {
        return User::where('id', $id)->update($data);
    }

    public function destroy(int $id): bool
    {
        // TODO: Implement destroy() method.
    }

    public function find(int $id, array $relations = []): array
    {
        $user = User::with($relations)->whereId($id)->first();

        return $user ? $user->toArray() : [];
    }

    public function get(array $filter = []): array
    {
        // TODO: Implement get() method.
    }

    public function findByEmail(string $email): array
    {
        $user = User::where('email', $email)->where('enabled', 1)->first();

        return $user ? $user->toArray() : [];
    }
}
