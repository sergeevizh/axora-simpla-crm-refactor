<?php

namespace App;

use App\Helpers\ConfigHelper;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;
use RedBeanPHP\R as R;

class DB
{
    private $config;

    private $eloquentCapsule;

    public function __construct(ConfigHelper $config)
    {
        $this->config = $config;

//        $this->redBeanSetup();
        $this->eloquentSetup();
    }

    private function redBeanSetup()
    {
        $dsn = "mysql:host={$this->config->DB_HOST};dbname={$this->config->DB_NAME}";
        R::setup($dsn, $this->config->DB_USER, $this->config->DB_PASSWORD);

        if (!R::testConnection()) {
            die("нет cоединения с бд");
        }

        if ($this->config->APP_DEBUG) {
            R::freeze(true);
        }
    }

    private function eloquentSetup()
    {
        $capsule = new Capsule;
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => $this->config->db_server,
            'database' => $this->config->db_name,
            'username' => $this->config->db_user,
            'password' => $this->config->db_password,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => $this->config->db_prefix,
        ]);

        $capsule->setEventDispatcher(new Dispatcher(new Container));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $this->eloquentCapsule = $capsule;
    }

    public function getCapsule()
    {
        return $this->eloquentCapsule;
    }
}