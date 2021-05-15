<?php

use App\Api\Axora;
use App\Design;
use App\Eloquent\Queries\EloquentAuthProviderQueries;
use App\Eloquent\Queries\EloquentUserQueries;
use App\Helpers\ConfigHelper;
use App\Repositories\IUserDBRepository;
use App\Utils\AuthManager;
use App\Utils\IAuthProvider;
use App\Utils\PHPDebugBarEloquentCollector;
use DebugBar\StandardDebugBar;
use Psr\Container\ContainerInterface;
use Rakit\Validation\Validator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use function DI\create;

return [



    Session::class => new Session(),

    Axora::class => static function () {
        return new Axora();
    },

    ConfigHelper::class => static function () {
        return new ConfigHelper();
    },

    Validator::class => static function () {
        return new Validator();
    },

    AuthManager::class => static function (ContainerInterface $c) {
        return new AuthManager($c->get(IAuthProvider::class), $c->get(Session::class));
    },

    'debugbar' => static function (ContainerInterface $c) {
        $debugbar = new StandardDebugBar();
        $debugbar->addCollector(new PHPDebugBarEloquentCollector($c));

        return $debugbar;
    },

    //todo: refactoring
    Design::class => static function (ContainerInterface $c) {
        $design = new Design($c->get(ConfigHelper::class), \axora()->settings);

        $design->assign('user', auth()->user());

        if ($c->get(ConfigHelper::class)->debug) {
            $design->assign('debugbarRenderer', $c->get('debugbar')->getJavascriptRenderer());
        }

        return $design;
    },

    /**
     * https://symfony.com/doc/current/components/http_foundation.html
     */
    Request::class => static function () {
        return new Request(
            $_GET,
            $_POST,
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    },

    IUserDBRepository::class => create(EloquentUserQueries::class),
    IAuthProvider::class => create(EloquentAuthProviderQueries::class),
];
