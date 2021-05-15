<?php

use App\Api\Axora;
use App\ContainerInstance;
use App\Utils\AuthManager;
use Rakit\Validation\Validator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

function debug($arr)
{
    echo '<pre>' . print_r($arr, true) . '</pre>';
}

function enableErrors()
{
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function asset(string $path)
{
    return  '/design/' . $_SERVER['CURRENT_DESIGN_THEME'] . '/' . $path;
}

function axora()
{
    return new Axora();
}

/**
 * Функция для смены кодировки строки
 *
 * @param string $str
 * @param  $to_encoding
 * @param  $from_encoding
 * @param bool $alt
 * @return bool|string
 */
function convert_str_encoding($str, $to_encoding, $from_encoding, $alt = false)
{
    if (function_exists('iconv')) {
        $str = @iconv($from_encoding, $to_encoding, $str);
    } elseif (function_exists('mb_convert_encoding')) {
        $str = @mb_convert_encoding($str, $to_encoding, $from_encoding);
    } else {
        // TODO add сonverting Windows-1251 to UTF-8 and the reverse when no iconv and mb_convert_encoding
        return $alt ? $alt : $str;
    }

    return $str;
}

function getValidUri()
{
    $uri = $_SERVER['REQUEST_URI'];

    if (false !== $pos = strpos($uri, '?')) {
        $uri = substr($uri, 0, $pos);
    }
    return rawurldecode($uri);
}

function makeValidation(Validator $validator, Request $request, array $rules)
{
    $validation = $validator->make($request->request->all() + $request->files->all(), $rules);

    $validation->validate();

    return $validation;
}


if (!function_exists('route')) {
    function route(string $url): string
    {
        return $url;
    }
}

if (!function_exists('redirect')) {
    function redirect(string $url, int $status = 302, array $headers = []): RedirectResponse
    {
        return (new RedirectResponse($url, $status, $headers))->send();
    }
}

if (!function_exists('back')) {
    function back()
    {
        return redirect(
            app()->get(Request::class)->headers->get('referer')
        );
    }
}

if (!function_exists('app')) {
    function app()
    {
        return ContainerInstance::get();
    }
}

if (!function_exists('session')) {
    function session()
    {
        return app()->get(Session::class);
    }
}

if (!function_exists('auth')) {
    function auth()
    {
        return app()->get(AuthManager::class);
    }
}