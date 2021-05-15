<?php

namespace App\Controllers;

use App\Design;
use Psr\Container\ContainerInterface;

class MainController extends Controller
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var Design
     */
    private $design;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->design = $container->get(Design::class);
    }

    public function index()
    {
        $this->design->render('main.tpl');
    }
}
