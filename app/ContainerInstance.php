<?php

namespace App;

use DI\Container;

class ContainerInstance
{
    protected static $container;

    public static function set(Container $container)
    {
        self::$container = $container;
    }

    public static function get(): Container
    {
        return self::$container;
    }
}