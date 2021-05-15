<?php

namespace App\Utils;

use App\DB;
use DebugBar\DataCollector\PDO\PDOCollector;
use DebugBar\DataCollector\PDO\TraceablePDO;
use Illuminate\Database\Capsule\Manager;
use PDO;
use Psr\Container\ContainerInterface;

class PHPDebugBarEloquentCollector extends PDOCollector
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->addConnection($this->getTraceablePdo($container), 'Eloquent PDO');
    }

    protected function getEloquentCapsule(ContainerInterface $container)
    {
        return $container->get(DB::class)->getCapsule();
    }

    protected function getEloquentPdo(ContainerInterface $container)
    {
        return $this->getEloquentCapsule($container)->getConnection()->getPdo();
    }

    protected function getTraceablePdo(ContainerInterface $container)
    {
        return new TraceablePDO($this->getEloquentPdo($container));
    }

    // Override
    public function getName()
    {
        return "eloquent_pdo";
    }

    // Override
    public function getWidgets()
    {
        return array(
            "eloquent" => array(
                "icon" => "inbox",
                "widget" => "PhpDebugBar.Widgets.SQLQueriesWidget",
                "map" => "eloquent_pdo",
                "default" => "[]"
            ),
            "eloquent:badge" => array(
                "map" => "eloquent_pdo.nb_statements",
                "default" => 0
            )
        );
    }
}