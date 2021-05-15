<?php

use App\Design;
use App\ContainerInstance;
use App\DB;
use App\Helpers\ConfigHelper;
use DI\ContainerBuilder;
use Symfony\Component\HttpFoundation\Session\Session;

require_once('vendor/autoload.php');

enableErrors();

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions(__DIR__ . '/config/container.php');
$container = $containerBuilder->build();

$dispatcher = include_once(__DIR__ . '/config/routes.php');
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], getValidUri());

// Start session
$container->call([$container->get(Session::class), 'start']);

// DB init
$container->set(DB::class, (new DB($container->get(ConfigHelper::class))));

// Save container instance to static wrapper, need for globally calling
ContainerInstance::set($container);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $container->get(Design::class)->render('404.tpl');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];

        die('405 Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        [$controller, $method] = $routeInfo[1];
        $parameters = $routeInfo[2];

        // Call base controller methods
        $container->call([$controller, 'call'], [$container]);

        // Call route action
        $container->call([$controller, $method], $parameters);
        break;
}

