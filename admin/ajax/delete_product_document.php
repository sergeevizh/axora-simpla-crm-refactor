<?php
require '../../vendor/autoload.php';

use App\Api\Axora;


$result = [];
$axora = new Axora();

$doc_id = $_GET['id'];

if ($doc_id) {
    $document = $axora->document->getById($doc_id);

    if ($document) {
        $documentPath = $axora->config->root_dir . $axora->config->documents_dir . $document->document;


        if (file_exists($documentPath)) {
            unlink($documentPath);
        }
        $axora->document->delete($doc_id);

    } else {
        $result = ['success' => false, 'error' => 'record not found' ];
    }
} else {
    $result = ['success' => false, 'error' => 'id not found' ];
}


if (empty($result)) {
    $result = ['success' => true];

}


header("Content-type: application/json; charset=UTF-8");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: -1");

print json_encode($result);
