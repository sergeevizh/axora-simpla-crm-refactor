<?php
require '../../vendor/autoload.php';

use App\Api\Axora;

    $axora = new Axora();
    $limit = 100;

    if (!$axora->managers->access('orders')) {
        return false;
    }

    $keyword = $axora->request->get('query', 'string');

    $keywords = explode(' ', $keyword);
    $keyword_sql = '';
    foreach ($keywords as $keyword) {
        $kw = $axora->db->escape(trim($keyword));
        $keyword_sql .= $axora->db->placehold("AND (p.name LIKE '%$kw%' OR p.meta_keywords LIKE '%$kw%' OR p.id in (SELECT product_id FROM __variants WHERE sku LIKE '%$kw%'))");
    }


    $axora->db->query('SELECT p.id, p.name, i.filename as image FROM __products p
	                    LEFT JOIN __images i ON i.product_id=p.id AND i.position=(SELECT MIN(position) FROM __images WHERE product_id=p.id LIMIT 1)
	                    LEFT JOIN __variants pv ON pv.product_id=p.id AND (pv.stock IS NULL OR pv.stock>0)
	                    WHERE 1 '.$keyword_sql.' AND pv.id
	                    GROUP BY p.id
	                    ORDER BY p.name LIMIT ?', $limit);
    foreach ($axora->db->results() as $product) {
        $products[$product->id] = $product;
    }

    $variants = array();
    if (!empty($products)) {
        $axora->db->query('SELECT v.id, v.name, v.sku, v.price, IFNULL(v.stock, ?) as stock, (v.stock IS NULL) as infinity, v.product_id FROM __variants v WHERE v.product_id in(?@) AND (v.stock IS NULL OR v.stock>0) AND v.price>0 ORDER BY v.position', $axora->settings->max_order_amount, array_keys($products));
        $variants = $axora->db->results();
    }

    foreach ($variants as $variant) {
        if (isset($products[$variant->product_id])) {
            $products[$variant->product_id]->variants[] = $variant;
        }
    }

    $suggestions = array();
    foreach ($products as $product) {
        if (!empty($product->variants)) {
            $suggestion = new \stdClass();
            if (!empty($product->image)) {
                $product->image = $axora->image->resize_image($product->image, 35, 35);
            }
            $suggestion->value = $product->name;
            $suggestion->data = $product;
            $suggestions[] = $suggestion;
        }
    }

    $res = new \stdClass();
    $res->query = $keyword;
    $res->suggestions = $suggestions;
    header("Content-type: application/json; charset=UTF-8");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: -1");
    print json_encode($res);
