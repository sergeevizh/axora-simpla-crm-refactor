<?php
require '../../vendor/autoload.php';

use App\Api\Axora;

$axora = new Axora();
$limit = 100;

$keyword = $axora->request->get('keyword', 'string');
if ($axora->request->get('limit', 'integer')) {
    $limit = $axora->request->get('limit', 'integer');
}

$orders = array_values($axora->orders->get_orders(array('keyword' => $keyword, 'limit' => $limit)));


header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: -1");
print json_encode($orders);
