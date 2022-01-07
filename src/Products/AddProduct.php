<?php
include_once ('../../vendor/autoload.php');

$product = new \App\Products\Add();

$sku = isset($_REQUEST['sku']) ? $_REQUEST['sku'] : '';
$name = isset($_REQUEST['name']) ? $_REQUEST['name'] : '';
$price = isset($_REQUEST['price']) ? $_REQUEST['price'] : '';
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';
$size = isset($_REQUEST['size']) ? $_REQUEST['size'] : '';
$weight = isset($_REQUEST['weight']) ? $_REQUEST['weight'] : '';
$height = isset($_REQUEST['height']) ? $_REQUEST['height'] : '';
$width = isset($_REQUEST['width']) ? $_REQUEST['width'] : '';
$length = isset($_REQUEST['length']) ? $_REQUEST['length'] : '';

$product->setSku($sku);
$product->setName($name);
$product->setPrice($price);
$product->setType($type);
$product->setSize($size);
$product->setWeight($weight);
$product->setHeight($height);
$product->setWidth($width);
$product->setLength($length);

$result = $product->addProduct();

echo json_encode($result)
?>