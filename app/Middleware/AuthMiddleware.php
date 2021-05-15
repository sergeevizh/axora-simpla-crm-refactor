<?php

namespace App\Middleware;

use App\Design;
use Psr\Container\ContainerInterface;

class AuthMiddleware
{
    public function handle(ContainerInterface $container)
    {
        if (!auth()->user()) {
            $container->get(Design::class)->render('404.tpl');
        }
    }
}