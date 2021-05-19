<?php

namespace App\Controllers;

use App\Middleware\AjaxMiddleware;
use App\Middleware\AuthMiddleware;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class Controller
{
    protected $middleware = [];

    private $middlewareMap = [
        'auth' => AuthMiddleware::class,
        'ajax' => AjaxMiddleware::class
    ];

    private function handleMiddleware(ContainerInterface $container)
    {
        if ($this->middleware) {
            foreach ($this->middleware as $middlewareAlias) {
                if (array_key_exists($middlewareAlias, $this->middlewareMap)) {
                    (new $this->middlewareMap[$middlewareAlias]())->handle($container);
                }
            }
        }
    }

    public function call(ContainerInterface $container)
    {
        $this->handleMiddleware($container);
    }

    /**
     * @param JsonResponse $response
     * @param array $data
     * @param int $status
     *
     * @return object|JsonResponse
     */
    public function jsonResponse(JsonResponse $response, array $data, int $status = 200)
    {
        return $response
            ->setData($data)
            ->setStatusCode($status)
            ->send();
    }
}
