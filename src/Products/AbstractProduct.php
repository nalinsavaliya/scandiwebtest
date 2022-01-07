<?php

namespace App\Products;
use PDO;

abstract class AbstractProduct extends \App\Connection
{
    //Product Type
    const TYPE_DVD = 'dvd';
    const TYPE_BOOK = 'book';
    const TYPE_FURNITURE = 'furniture';

    // Product table and fields
    const PRODUCT_TABLE = 'products';
    const PRODUCT_PRODUCT_ID = 'product_id';
    const PRODUCT_SKU = 'sku';
    const PRODUCT_NAME = 'name';
    const PRODUCT_PRICE = 'price';
    const PRODUCT_PRODUCT_TYPE = 'product_type';

    // Attribute table and fields
    const PRODUCT_ATTRIBUTE_TABLE = 'attribute';
    const ATTRIBUTE_ATTRIBUTE_ID = 'attribute_id';
    const ATTRIBUTE_PRODUCT_ID = 'product_id';
    const ATTRIBUTE_ATTRIBUTE_LABEL = 'attribute_label';
    const ATTRIBUTE_ATTRIBUTE_VALUE = 'attribute_value';

    /**
     * @var $products
     */
    protected $products;
    /**
     * @var $id
     */
    protected $id;
    /**
     * @var $sku
     */
    protected $sku;
    /**
     * @var $name
     */
    protected $name;
    /**
     * @var $price
     */
    protected $price;
    /**
     * @var $type
     */
    protected $type;

    /**
     * @return array
     */
    public function getProducts()
	{
   	    if(!$this->products) {
			$stmt = $this->con->prepare("SELECT * FROM " . self::PRODUCT_TABLE);

	        $stmt->execute();

	        $this->products = $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		return $this->products;
	}

    /**
     * @return array
     */
    public function addProduct() {
        $result = [];
        try{
            $sku = $this->getSku();
            $name = $this->getName();
            $price = $this->getPrice();
            $type = $this->getType();

            if($this->checkSkuExists($sku)){
                $result['success'] = false;
                $result['message'] = '"'. $sku .'" SKU already exists. Please add a unique SKU.';
                return $result;
            }
            $stmt = $this->con->prepare("INSERT INTO ".self::PRODUCT_TABLE."(`". self::PRODUCT_SKU ."`, `". self::PRODUCT_NAME ."`, `".self::PRODUCT_PRICE ."`, `". self::PRODUCT_PRODUCT_TYPE ."`) VALUES ('$sku', '$name', '$price', '$type')");
            $stmt->execute();

            $lastInsertId = $this->con->lastInsertId();
            $result = $this->setAttributes($lastInsertId, $type);
            if($result['success']){
                $result['success'] = true;
                $result['message'] = 'Product added successfully.';
            }else{
                return $result;
            }
        }catch (\Exception $e){
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $lastInsertId
     * @param $type
     *
     * @return array
     */
    public function setAttributes($lastInsertId, $type){
        $result = [];
        try{
            $size = $this->getSize();
            $weight = $this->getWeight();
            $height = $this->getHeight();
            $width = $this->getWidth();
            $length = $this->getLength();

            $attributes = [];
            if($type === self::TYPE_DVD){
                $attributes = [
                    'dvd' => $size
                ];
            }
            if($type === self::TYPE_BOOK){
                $attributes = [
                    'weight' => $weight
                ];
            }
            if($type === self::TYPE_FURNITURE){
                $attributes = [
                    'height' => $height,
                    'width' => $width,
                    'length' => $length,
                ];
            }

            foreach ($attributes as $key => $value){
                $stmt = $this->con->prepare("INSERT INTO ". self::PRODUCT_ATTRIBUTE_TABLE ."(`".self::ATTRIBUTE_PRODUCT_ID."`, `".self::ATTRIBUTE_ATTRIBUTE_LABEL."`, `".self::ATTRIBUTE_ATTRIBUTE_VALUE."`) VALUES ('$lastInsertId', '$key', '$value')");
                $stmt->execute();
            }

            $result['success'] = true;
            $result['message'] = 'Product attribute added successfully.';
        }catch (\Exception $e){
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }
        return $result;

    }

    /**
     * @param $productId
     *
     * @return array
     */
    public function getAttributeByProductId($productId){
        $stmt = $this->con->prepare("SELECT *  FROM ".self::PRODUCT_ATTRIBUTE_TABLE." WHERE `".self::ATTRIBUTE_PRODUCT_ID."` = $productId");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $attributes
     * @param $type
     *
     * @return string
     */
    public function attributeDisplay($attributes, $type){
        $displayValue = '';
        $preFix = '';
        $totalValue = count($attributes);
        $i = 1;
        foreach ($attributes as $attribute){

            if($type === self::TYPE_DVD){
                $preFix = 'Size:';
                $displayValue .= $attribute['attribute_value'] . " MB";
            }
            if($type === self::TYPE_BOOK){
                $preFix = 'Weight:';
                $displayValue .= $attribute['attribute_value']. " KG";
            }
            if($type === self::TYPE_FURNITURE){
                $preFix = 'Dimension:';
                $displayValue .= $attribute['attribute_value'];
                if($totalValue !== $i){
                    $displayValue .= 'x';
                }
            }
            $i++;
        }
        return $preFix ." ". $displayValue;
    }

    /**
     * @param $ids
     *
     * @return array
     */
    public function deleteItem($ids) {
        $result = [];
        try{
            $stmt = $this->con->prepare("DELETE FROM ". self::PRODUCT_TABLE ." WHERE ". self::PRODUCT_PRODUCT_ID ." IN ($ids)");
            $stmt->execute();
            $result['success'] = true;
            $result['message'] = 'Products deleted successfully.';
        }catch (\Exception $e){
            $result['success'] = false;
            $result['message'] = $e->getMessage();
        }
        return $result;
    }

    /**
     * @param $sku
     *
     * @return bool
     */
    public function checkSkuExists($sku){
        $stmt = $this->con->prepare("SELECT * FROM ". self::PRODUCT_TABLE ." where ". self::PRODUCT_SKU ." = '$sku'");
        $stmt->execute();
        $skuExists = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(count($skuExists)){ return true; }

        return false;
    }

    /**
     * @return mixed
     */
    public function getSku() {
        return $this->sku;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setSku($value) {
        return $this->sku = $value;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setName($value) {
        return $this->name = $value;
    }

    /**
     * @return mixed
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setPrice($value) {
        return $this->price = $value;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setType($value) {
        return $this->type = $value;
    }

    /**
     * @return mixed
     */
    public function getSize() {
        return $this->size;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setSize($value) {
        return $this->size = $value;
    }

    /**
     * @return mixed
     */
    public function getWeight() {
        return $this->weight;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setWeight($value) {
        return $this->weight = $value;
    }

    /**
     * @return mixed
     */
    public function getHeight() {
        return $this->height;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setHeight($value) {
        return $this->height = $value;
    }

    /**
     * @return mixed
     */
    public function getWidth() {
        return $this->width;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setWidth($value) {
        return $this->width = $value;
    }

    /**
     * @return mixed
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setLength($value) {
        return $this->length = $value;
    }

}