<?php

namespace App\Controllers;

use App\Design;
use App\Repositories\IBannerDBRepository;
use App\Repositories\IBlogDBRepository;
use App\Repositories\IProductDBRepository;
use Psr\Container\ContainerInterface;

class MainController extends Controller
{
    private $container;
    private $design;
    private $bannerDBRepository;
    private $productDBRepository;
    private $blogDBRepository;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->design = $container->get(Design::class);
        $this->bannerDBRepository = $container->get(IBannerDBRepository::class);
        $this->productDBRepository = $container->get(IProductDBRepository::class);
        $this->blogDBRepository = $container->get(IBlogDBRepository::class);

    }

    public function index()
    {
        $this->design->assign('banners_slider', $this->bannerDBRepository->get());
        $this->design->assign('new_products', $this->productDBRepository->get('new'));
        $this->design->assign('featured_products', $this->productDBRepository->get('featured'));
        $this->design->assign('discounted_products', $this->productDBRepository->get('discounted'));
        $this->design->assign('posts', $this->blogDBRepository->get());

        $this->design->render('main.tpl');
    }
}
