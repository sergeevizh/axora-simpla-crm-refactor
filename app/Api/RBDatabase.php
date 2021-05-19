<?php

namespace App\Api;

class RBDatabase extends Axora
{
    use TSingleton;

    public function __construct()
    {
        parent::__construct();

        try {
            $this->connect();
        } catch (\Exception $e) {
        }
    }

    private function connect()
    {
        class_alias('\RedBeanPHP\R', '\R');

        \R::setup(
            "mysql:host={$this->config->db_server};dbname={$this->config->lrogiixt_simpla};charset={$this->config->db_charset}",
            $this->config->db_user,
            $this->config->db_password
        );
        if (!\R::testConnection()) {
            throw new \Exception('Нет соединения с бд', 500);
        }

        //запрещаем изменять бд
        \R::freeze(true);
        if (DEBUG) {
            \R::debug(true, 1);
        }
    }
}
