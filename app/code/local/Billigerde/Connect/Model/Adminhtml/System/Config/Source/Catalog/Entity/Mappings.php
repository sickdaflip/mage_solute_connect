<?php
/**
 *
 * @category   Billigerde
 * @package    Billigerde_Connect
 * @subpackage
 * @author   Steffen Kieckhäven  <steffen.kieckhaeven@shopping.de>
 *
 */
class Billigerde_Connect_Model_Adminhtml_System_Config_Source_Catalog_Entity_Mappings
{
    const SEPERATOR = '::';

    protected $_collection = null;
    protected $_typeCatalogProduct = null;
    protected $_typeCatalogCategory = null;
    protected $_getter = null;

    public function __construct()
    {
        $this->_getter = array(
            'getFinalPrice' => 'Final Price',
            'getGroupPrice' => 'Group Price',
            'getProductUrl' => 'Product Url',
            'getStockItem' .  self::SEPERATOR . 'getQty' => 'StockItem Qty',
            'getStockItem' .  self::SEPERATOR . 'getMinSaleQty' => 'StockItem Min Sale Qty',
            'getStockItem' .  self::SEPERATOR . 'getMaxSaleQty' => 'StockItem Max Sale Qty',
        );
    }

    public function getTypeCatalogProduct()
    {
        if ($this->_typeCatalogProduct == null) {
            /** @var Mage_Eav_Model_Entity_Type $typeCatalogProduct */
            $typeCatalogProduct = mage::getModel('eav/entity_type');
            $typeCatalogProduct->loadByCode(Mage_Catalog_Model_Product::ENTITY);
            $this->_typeCatalogProduct = $typeCatalogProduct;
        }
        return $this->_typeCatalogProduct;
    }

    public function getTypeCatalogCategory()
    {
        if ($this->_typeCatalogCategory == null) {
            /** @var Mage_Eav_Model_Entity_Type $typeCatalogCategory */
            $typeCatalogCategory = mage::getModel('eav/entity_type');
            $typeCatalogCategory->loadByCode(Mage_Catalog_Model_Category::ENTITY);
            $this->_typeCatalogCategory = $typeCatalogCategory;
        }
        return $this->_typeCatalogCategory;
    }

    public function getCollection()
    {
        if ($this->_collection == null) {
            /** @var Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection */
            $collection = mage::getModel('eav/entity_attribute')->getCollection();
            $collection->addFieldToFilter('entity_type_id', array('in' => array($this->getTypeCatalogCategory()->getId(), $this->getTypeCatalogProduct()->getId())));
            $collection->addOrder('entity_type_id', 'desc');
            $collection->addOrder('attribute_code', 'asc');
            $this->_collection = $collection;
        }
        return $this->_collection;
    }


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = array();

        foreach ($this->toArray() as $key => $value) {
        	$return[] = array('value' => $key, 'label' => $value);
        }

        return $return;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $mappings = array('0' => '');

        foreach ($this->_getter as $key => $value)
        {
            $mappings[$key] = $this->getGetterValue($key, $value);
        }

        /** @var Mage_Eav_Model_Entity_Attribute $attribute */
        foreach ($this->getCollection() as $key => $attribute) {
            $mappings[$key] = $this->getAttributeValue($attribute);
        }

        $mappings = new Varien_Object($mappings);

        mage::dispatchEvent('billigerde_connect_fetch_mappings', array('mappings' => $mappings));

        return $mappings->getData();
    }

    public function getGetterValue($key, $value)
    {
        return 'getter' . self::SEPERATOR . $value;
    }

    public function getAttributeValue(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        if ($attribute->getEntityTypeId() == $this->getTypeCatalogCategory()->getId()) {
            $prefix = Mage_Catalog_Model_Category::ENTITY;
        } else if ($attribute->getEntityTypeId() == $this->getTypeCatalogProduct()->getId()) {
            $prefix = Mage_Catalog_Model_Product::ENTITY;
        } else {
            $prefix = '';
        }

        return $prefix . self::SEPERATOR . $attribute->getAttributeCode();
    }
}