<?php

namespace App\Middleware;

use Psr\Container\ContainerInterface;
use RuntimeException;

class AjaxMiddleware
{
    public function handle(ContainerInterface $container)
    {
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        if (!$isAjax) {
            throw new RuntimeException('Does not ajax method', 400);
        }

        //todo: не уверен что нужны эти заголовки
        header("Content-type: application/json; charset=UTF-8");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("X-Robots-Tag: noindex, noarchive, nosnippet");
        header("Pragma: no-cache");
        header("Expires: -1");
    }
}