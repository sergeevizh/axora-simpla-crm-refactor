<?php
require '../../vendor/autoload.php';
session_start();


use App\Api\Axora;


$axora = new Axora();

// Проверка сессии для защиты от xss
if (!$axora->request->check_session()) {
    trigger_error('Session expired', E_USER_WARNING);
    exit();
}

$id = intval($axora->request->post('id'));
$object = $axora->request->post('object');
$values = $axora->request->post('values');

switch ($object) {

 case 'categories_features':
        if ($axora->managers->access('features')) {
            $id = $values['feature_id'];
            $categories = $values['category_id'];
            $result = $axora->features->update_feature_categories($id, (array)$categories,$values);
        }
        break;
    case 'product':
        if ($axora->managers->access('products')) {
            $result = $axora->products->update_product($id, $values);
        }
        break;
    case 'category':
        if ($axora->managers->access('categories')) {
            $result = $axora->categories->update_category($id, $values);
        }
        break;
    case 'brands':
        if ($axora->managers->access('brands')) {
            $result = $axora->brands->update_brand($id, $values);
        }
        break;
    case 'feature':
        if ($axora->managers->access('features')) {
            $result = $axora->features->update_feature($id, $values);
        }
        break;
    case 'page':
        if ($axora->managers->access('pages')) {
            $result = $axora->pages->update_page($id, $values);
        }
        break;
    case 'blog':
        if ($axora->managers->access('blog')) {
            $result = $axora->blog->update_post($id, $values);
        }
        break;
    case 'delivery':
        if ($axora->managers->access('delivery')) {
            $result = $axora->delivery->update_delivery($id, $values);
        }
        break;
    case 'payment':
        if ($axora->managers->access('payment')) {
            $result = $axora->payment->update_payment_method($id, $values);
        }
        break;
    case 'currency':
        if ($axora->managers->access('currency')) {
            $result = $axora->money->update_currency($id, $values);
        }
        break;
    case 'comment':
        if ($axora->managers->access('comments')) {
            $result = $axora->comments->update_comment($id, $values);
        }
        break;
    case 'user':
        if ($axora->managers->access('users')) {
            $result = $axora->users->update_user($id, $values);
        }
        break;
    case 'label':
        if ($axora->managers->access('labels')) {
            $result = $axora->orders->update_label($id, $values);
        }
        break;
    case 'tags':
        if ($axora->managers->access('tags')) {
            $result = $axora->tags->update_tag($id, $values);
        }
        break;


}

header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: -1");
$json = json_encode($result);
print $json;
