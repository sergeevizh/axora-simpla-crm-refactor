<?php
require '../../vendor/autoload.php';
use App\Api\Axora;
    $axora = new Axora();
    $limit = 100;

    $keyword = $axora->request->get('query', 'string');

    $axora->db->query('SELECT u.id, u.name, u.email FROM __users u WHERE u.name LIKE "%'.$axora->db->escape($keyword).'%" OR u.email LIKE "%'.$axora->db->escape($keyword).'%"ORDER BY u.name LIMIT ?', $limit);
    $users = $axora->db->results();

    $suggestions = array();
    foreach ($users as $user) {
        $suggestion = new \stdClass();
        $suggestion->value = $user->name." ($user->email)";
        $suggestion->data = $user;
        $suggestions[] = $suggestion;
    }

    $res = new \stdClass();
    $res->query = $keyword;
    $res->suggestions = $suggestions;

    header("Content-type: application/json; charset=UTF-8");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: -1");

    print json_encode($res);
