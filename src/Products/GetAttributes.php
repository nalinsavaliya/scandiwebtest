<?php
include_once ('../../vendor/autoload.php');

$product = new \App\Products\ProductList();

$typeId = ($_REQUEST['type_id']) ?: '';
$result = '';
if($typeId) {
    $result = $product->getAttributesByTypeId($typeId);
}
echo json_encode($result)
?>