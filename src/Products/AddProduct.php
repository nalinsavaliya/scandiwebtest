<?php
include_once ('../../vendor/autoload.php');

$product = new \App\Products\Add();

$sku = isset($_REQUEST['sku']) ? $_REQUEST['sku'] : '';
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$price = isset($_REQUEST['price']) ? $_REQUEST['price'] : '';
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$attribute = isset($_REQUEST['attribute']) ? $_REQUEST['attribute'] : '';

$product->setSku($sku);
$product->setName($name);
$product->setPrice($price);
$product->setType($type);
$product->setAttributesData($attribute);

$result = $product->addProduct();

echo json_encode($result)
?>