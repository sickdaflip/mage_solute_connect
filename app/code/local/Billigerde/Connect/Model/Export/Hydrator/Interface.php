<?php
/**
 *
 * @desc
 *
 * @category   Billigerde
 * @package    Billigerde_Connect
 * @subpackage
 * @author   Steffen Kieckhäven  <steffen.kieckhaeven@shopping.de>
 *
 */
Interface Billigerde_Connect_Model_Export_Hydrator_Interface
{

    public function map();

    /**
     * @return Varien_Object
     */
    public function getFieldDefinition();
    public function setFieldDefinition(Varien_Object $fieldDefinition);

    public function getMappedValue();
    public function setMappedValue($value);

    public function getResult();
    public function setResult($result);

    public function getProduct();
    public function setProduct(Mage_Catalog_Model_Product $product);

    public function getCategory();
    public function setCategory(Mage_Catalog_Model_Category $category);
}