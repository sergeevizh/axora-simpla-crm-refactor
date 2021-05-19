<?php

namespace App\Eloquent\Queries;

use App\Eloquent\Models\User;
use App\Utils\IAuthProvider;

class EloquentAuthProviderQueries implements IAuthProvider
{
    public function getUser(int $id): array
    {
        $user = User::find($id);

        return $user ? $user->toArray() : [];
    }
}
