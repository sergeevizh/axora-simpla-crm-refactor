<?php

namespace App\Utils;

interface IAuthProvider
{
    public function getUser(int $id): array;
}