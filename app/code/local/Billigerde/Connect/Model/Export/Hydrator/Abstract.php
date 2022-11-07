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
abstract class Billigerde_Connect_Model_Export_Hydrator_Abstract implements Billigerde_Connect_Model_Export_Hydrator_Interface
{

    protected $_product = null;
    protected $_category = null;

    protected $_fieldDefinition = null;
    protected $_result = null;

    protected $_mappedValue = null;

    protected function _beforeMap()
    {
        $this->unsetResult();
        return $this;
    }

    public function setFieldDefinition(Varien_Object $fieldDefinition)
    {
        $this->_fieldDefinition = $fieldDefinition;
    }

    public function getFieldDefinition()
    {
        if ($this->_fieldDefinition == null) {
            $this->_fieldDefinition = new Varien_Object();
        }
        return $this->_fieldDefinition;
    }

    public function getMappedValue()
    {
        return $this->_mappedValue;
    }

    public function setMappedValue($value)
    {
        $this->_mappedValue = $value;
        return $this;
    }

    protected function _afterMap()
    {
        $formatter = $this->getFieldDefinition()->getFormatter();

        if ($formatter instanceof Billigerde_Connect_Model_Export_Formatter_Interface) {
            $this->setResult($formatter->format($this->getResult()));
        }

        return $this;
    }

    public function getProduct()
    {
        return $this->_product;
    }

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getCategory()
    {
        return $this->_category;
    }

    public function setCategory(Mage_Catalog_Model_Category $category)
    {
        $this->_category = $category;
        return $this;
    }

    final public function map()
    {
        $this->_beforeMap();
        $this->mapValue();
        $this->_afterMap();
    }

    abstract public function mapValue();

    public function getResult()
    {
        return $this->_result;
    }

    public function setResult($result)
    {
        $this->_result = $result;
        return $this;
    }

    public function unsetResult()
    {
        $this->_result = null;
        return $this;
    }

}