<?php

namespace App\Api;

trait TSingleton
{
    private static $instance;
    public static function instance()
    {
        /*
         * если свойство пустое, то ложим в него обьект
         */
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
