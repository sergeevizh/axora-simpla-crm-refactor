<?php
require '../../vendor/autoload.php';

use App\Api\Axora;

    $axora = new Axora();
    $limit = 100;


    $keyword = $axora->request->get('query', 'string');
    $feature_id = $axora->request->get('feature_id', 'string');

    $query = $axora->db->placehold('SELECT DISTINCT po.value FROM __options po
										WHERE value LIKE "'.$axora->db->escape($keyword).'%" AND feature_id=? ORDER BY po.value LIMIT ?', $feature_id, $limit);

    $axora->db->query($query);

    $options = $axora->db->results('value');

    $res = new \stdClass();
    $res->query = $keyword;
    $res->suggestions = $options;
    header("Content-type: application/json; charset=UTF-8");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    print json_encode($res);
