<?php

use App\Controllers\Ajax\AjaxAuthController;
use App\Controllers\AuthController;
use App\Controllers\BlogController;
use App\Controllers\MainController;
use App\Controllers\UserController;
use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

return $dispatcher = simpleDispatcher(function (RouteCollector $r) {

    $r->get('/', [MainController::class, 'index']);
    $r->get('/blog', [BlogController::class, 'index']);

    $r->addGroup('/user', function (RouteCollector $r) {
        $r->get('', [UserController::class, 'index']);
        $r->get('/edit', [UserController::class, 'edit']);
        $r->post('/update', [UserController::class, 'update']);
        $r->get('/logout', [AuthController::class, 'logout']);
    });

    $r->addGroup('/ajax', function (RouteCollector $r) {
        $r->addGroup('/auth', function (RouteCollector $r) {
            $r->post('/login', [AjaxAuthController::class, 'login']);
            $r->post('/register', [AjaxAuthController::class, 'register']);
        });
    });
});