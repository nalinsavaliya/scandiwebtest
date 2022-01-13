<?php

namespace App\Products;
use PDO;

abstract class AbstractProduct extends \App\Connection
{
    //productType table and fields
    const PRODUCT_TYPE_TABLE = 'product_type';
    const PRODUCT_TYPE_ID = 'id';
    const PRODUCT_TYPE_TYPE = 'type';

    // Product table and fields
    const PRODUCT_TABLE = 'products';
    const PRODUCT_PRODUCT_ID = 'product_id';
    const PRODUCT_SKU = 'sku';
    const PRODUCT_NAME = 'name';
    const PRODUCT_PRICE = 'price';
    const PRODUCT_PRODUCT_TYPE = 'product_type';

    // Attribute table and fields
    CONST ATTRIBUTE_TABLE = 'attribute';
    CONST ATTRIBUTE_ID = 'id';
    CONST ATTRIBUTE_PRODUCT_TYPE = 'product_type';
    CONST ATTRIBUTE_CODE = 'code';
    CONST ATTRIBUTE_LABEL = 'label';
    CONST ATTRIBUTE_TYPE = 'type';
    CONST ATTRIBUTE_DISPLAY_PRE_FIX = 'display_pre_fix';
    CONST ATTRIBUTE_DISPLAY_POST_FIX = 'display_post_fix';
    CONST ATTRIBUTE_MESSAGE = 'message';

    // Attribute value table and fields
    const PRODUCT_ATTRIBUTE_TABLE = 'attribute_value';
    const ATTRIBUTE_VALUE_ATTRIBUTE_ID = 'id';
    const ATTRIBUTE_PRODUCT_ID = 'product_id';
    const ATTRIBUTE_ATTRIBUTE_ID = 'attribute_id';
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
     * @var $attributes
     */
    protected $attributes;

    /**
     * @return array
     */
    public function getProductTypes()
    {
        $stmt = $this->con->prepare("SELECT * FROM " . self::PRODUCT_TYPE_TABLE);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * @return array
     */
    public function getAttributesByTypeId($typeId)
    {

        $stmt = $this->con->prepare("SELECT * FROM " . self::ATTRIBUTE_TABLE. " WHERE ". self::ATTRIBUTE_PRODUCT_TYPE . "=" . $typeId);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
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
            $attributes = $this->getAttributesData();
            foreach ($attributes as $key => $value){
                $stmt = $this->con->prepare("INSERT INTO ". self::PRODUCT_ATTRIBUTE_TABLE ."(`".self::ATTRIBUTE_PRODUCT_ID."`, `".self::ATTRIBUTE_ATTRIBUTE_ID."`, `".self::ATTRIBUTE_ATTRIBUTE_VALUE."`) VALUES ('$lastInsertId', '$key', '$value')");
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

        $stmt = $this->con->prepare("SELECT * FROM ".self::PRODUCT_ATTRIBUTE_TABLE." JOIN ". self::ATTRIBUTE_TABLE . " ON ".self::PRODUCT_ATTRIBUTE_TABLE.".".self::ATTRIBUTE_ATTRIBUTE_ID." = ".self::ATTRIBUTE_TABLE.".".self::ATTRIBUTE_ID." WHERE `".self::ATTRIBUTE_PRODUCT_ID."` = ". $productId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param $attributes
     * @param $type
     *
     * @return string
     */
    public function attributeDisplay($attributes){
        $displayValue = '';

        $preFix = '';
        $attributeValue = [];
        $postFix = '';
        foreach ($attributes as $attribute){
            $preFix = $attribute['display_pre_fix'];
            $attributeValue[] = $attribute['attribute_value'];
            $postFix = $attribute['display_post_fix'];
            if($preFix === ''){
                $preFix = $attribute['label'];
            }
        }
        $attributeValue = implode('x', $attributeValue);
        $displayValue .= $preFix . ": ". $attributeValue . " ". $postFix;
        return $displayValue;
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
    public function setAttributesData($value) {
        return $this->attributes = $value;
    }

    /**
     * @return mixed
     */
    public function getAttributesData() {
        return $this->attributes;
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