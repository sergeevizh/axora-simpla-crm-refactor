<?php
die('tut');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


chdir('..');

// Засекаем время
$time_start = microtime(true);
session_start();
$_SESSION['id'] = session_id();

@ini_set('session.gc_maxlifetime', 86400); // 86400 = 24 часа
@ini_set('session.cookie_lifetime', 0); // 0 - пока браузер не закрыт


// Кеширование в админке нам не нужно
Header("Cache-Control: no-cache, must-revalidate");
header("Expires: -1");
Header("Pragma: no-cache");

require_once (__DIR__ . '/../vendor/autoload.php');
require_once (__DIR__ . '/../config/functions.php');

use Axora\IndexAdmin;

// Установим переменную сессии, чтоб фронтенд нас узнал как админа
$_SESSION['admin'] = 'admin';

$backend = new IndexAdmin();

// Проверка сессии для защиты от xss
if (!$backend->request->check_session()) {
    unset($_POST);
    header('Location: '.$_SERVER['REQUEST_URI']);
    trigger_error('Session expired', E_USER_WARNING);
    exit;
}

header("Content-type: text/html; charset=UTF-8");
print $backend->fetch();

// Отладочная информация
if ($backend->config->debug) {
    print "<!--\r\n";
    $exec_time = round(microtime(true)-$time_start, 5);

    $files = get_included_files();
    print "+-------------- included files (" . count($files) . ") --------------+\r\n\n";
    foreach ($files as $file) {
        print $file . " \r\n";
    }

    print "\n\n"."+------------- SQL (last 100 query) -------------+\r\n\n";
    $backend->db->query("SHOW profiles;");
    $total_time_sql = 0;
    $profiles_sql = $backend->db->results();

    foreach ($profiles_sql as $sql) {
        echo $sql->Query_ID . ': ' . $sql->Duration . 's: ' . $sql->Query . "\r\n";
        $total_time_sql += $sql->Duration;
    }
    print "\n" . count($profiles_sql) . " queries, " . $total_time_sql . "s" ;

    print "\n\n" . "+-------------- page generation time -------------+\r\n\n";
    print "page generation time: " . $exec_time . "s\r\n";

    if (function_exists('memory_get_peak_usage')) {
        print "\n\n" . "+--------------- memory peak usage ---------------+\r\n\n";
        print "memory peak usage: " . (round(memory_get_peak_usage() / 1048576 * 100) / 100) . " mb\r\n";
    }

    print "-->";
}
