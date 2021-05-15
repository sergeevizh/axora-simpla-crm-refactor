<?php

session_start();


use App\Api\Axora;

$axora = new Axora();

if (!$axora->managers->access('design')) {
    return false;
}

// Проверка сессии для защиты от xss
if (!$axora->request->check_session()) {
    trigger_error('Session expired', E_USER_WARNING);
    exit();
}
$content = $axora->request->post('content');
$style = $axora->request->post('style');
$theme = $axora->request->post('theme', 'string');

if (pathinfo($style, PATHINFO_EXTENSION) != 'css') {
    exit();
}

$file = $axora->config->root_dir.'design/'.$theme.'/css/'.$style;
if (is_file($file) && is_writable($file) && !is_file($axora->config->root_dir.'design/'.$theme.'/locked')) {
    file_put_contents($file, $content);
}

$result= true;
header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: -1");
$json = json_encode($result);
print $json;
