<?php
include_once ('../../vendor/autoload.php');

$deleteProduct = new \App\Products\Delete();

$productIds = isset($_REQUEST['product_ids']) ? $_REQUEST['product_ids'] : '';
$result = $deleteProduct->deleteItem($productIds);

echo json_encode($result)
?>