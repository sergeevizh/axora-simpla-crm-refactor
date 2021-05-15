<?php

namespace Axora;

use App\Api\Axora;

class StatsAdmin extends Axora
{

    public function fetch()
    {
        return $this->design->fetch('stats.tpl');
    }
}
