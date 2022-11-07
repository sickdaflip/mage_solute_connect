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
class Billigerde_Connect_Model_Service_Export extends Billigerde_Connect_Model_Service_Abstract
{
    protected $_attributes = array();

    public function exportProducts()
    {
        try {
            /** @var Billigerde_Connect_Model_Export_Profile $profile */
            $profile = mage::getModel('billigerde_connect/export_profile');

            foreach (mage::app()->getStores() as $store) {
                if (!$profile->isActiveForStore($store)) {
                    continue;
                }

                try {
                    $this->startEmulation($store);

                    // SUDO NO FLAT
                    mage::app()->getStore()->setConfig(Mage_Catalog_Helper_Category_Flat::XML_PATH_IS_ENABLED_FLAT_CATALOG_CATEGORY, 0);
                    mage::app()->getStore()->setConfig(Mage_Catalog_Helper_Product_Flat::XML_PATH_USE_PRODUCT_FLAT, 0);

                    $profile->debugLog('Export Start ' . $store->getId() . '_' . $store->getCode(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_FLOW);

                    $this->exportProductsForStore($profile, $store);

                    $profile->debugLog('Export Stop ' . $store->getId() . '_' . $store->getCode(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_FLOW);

                    $this->stopEmulation();

                } catch (Exception $e) {
                    $profile->debugLog('Export Fail ' . $store->getId() . '_' . $store->getCode(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_FLOW);
                    $profile->debugLog('Export Fail ' . $e->getMessage(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_FLOW);

                    $this->stopEmulation();
                    Mage::logException($e);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    public function exportProductsForStore(Billigerde_Connect_Model_Export_Profile $profile, Mage_Core_Model_Store $store)
    {

        /** @var Mage_Catalog_Model_Resource_Product_Collection $productCollection */
        $productCollection = mage::getModel('catalog/product')->getCollection();

        $profile->applyProductCollectionFilter($productCollection);

        $productCollection->addAttributeToSelect('*');
        $productCollection->setFlag('require_stock_items', true);

        /** @var Billigerde_Connect_Model_Export_Writer_Csv $writer */
        $writer = mage::getModel('billigerde_connect/export_writer_csv');
        $writer->setDelimiter($profile->getDelimiter());
        $writer->setEnclosure($profile->getEnclosure());
        $writer->setFileName($profile->getFileName());
        $writer->setFilePath($profile->getFilePath());
        $writer->setHeadline(array_keys($profile->getFields()));

        $profile->setWriter($writer);

        $writer->writeHeadline();

        $pageSize = Mage::getStoreConfig('billigerde_connect_setting/export/collection_page_size');
        $currentPage = 1;

        $productCollection->setPage($currentPage, $pageSize);

        $lastPage = $productCollection->getLastPageNumber();

        do {
            $productCollection->setCurPage($currentPage);

            $productCollection->load();

            $productCollection->addCategoryIds();
            /**
             * @var $product Mage_Catalog_Model_Product
             */
            foreach ($productCollection as $product) {
                $this->_exportProduct($product, $profile, $store);
            }

            $profile->debugLog('ProductCollection Select: ' . $productCollection->getSelectSql(true), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_FLOW);
            $profile->debugLog('CurrentPage: ' . $currentPage, Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_FLOW);

            $productCollection->clear();
            $currentPage++;

        } while ($currentPage <= $lastPage);

        return $this;
    }

    protected function _exportProductBefore(Mage_Catalog_Model_Product $product, Billigerde_Connect_Model_Export_Profile $profile, Mage_Core_Model_Store $store)
    {
        /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
        $categoryCollection = $product->getCategoryCollection();
        $categoryCollection->addAttributeToSort('level', 'desc');
        $categoryCollection->getSelect()->limit(1);
        $category = $categoryCollection->getFirstItem();

        if ($category && $category->getId()) {
            mage::register('current_category', $category);
            $product->setCategory($category);
            $product->setCategoryId($category->getId());
        }
        return $this;
    }

    protected function _exportProductAfter(Mage_Catalog_Model_Product $product, Billigerde_Connect_Model_Export_Profile $profile, Mage_Core_Model_Store $store)
    {
        mage::unregister('current_category');

        return $this;
    }

    protected function _exportProduct(Mage_Catalog_Model_Product $product, Billigerde_Connect_Model_Export_Profile $profile, Mage_Core_Model_Store $store)
    {
        $this->_exportProductBefore($product, $profile, $store);

        $profile->debugLog('ProductBaseData ' . $product->getId(). ' (' . get_class($product) . ') ' . $product->getTypeId() . ':', Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA);
        $profile->debugLog($product->debug(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA);

        $productData = array();
        foreach ($profile->getFields() as $fieldDefinition) {
            $productData[$fieldDefinition->getField()] = $this->_getProductFieldValue($fieldDefinition, $product, $profile, $store);
        }

        $profile->debugLog('ProductExportData ' . $product->getId(). ' (' . get_class($product) . ') ' . $product->getTypeId() . ':', Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA);
        $profile->debugLog($productData, Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA);

        /** @var Billigerde_Connect_Model_Export_Writer_Csv $writer */
        $writer = $profile->getWriter();
        $writer->addDataLineToFile($productData);

        $this->_exportProductAfter($product, $profile, $store);

        return $this;
    }

    protected function _getProductFieldValue(Varien_Object $fieldDefinition, Mage_Catalog_Model_Product $product, Billigerde_Connect_Model_Export_Profile $profile, Mage_Core_Model_Store $store)
    {
        $mapping = $fieldDefinition->getMapping();

        $profile->debugLog($fieldDefinition->debug(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);

        if (is_numeric($mapping)) {
            $value = $this->_getAttributeValueFromProduct($mapping, $product, $profile);
        } else {
            $value = $this->_getGetterValueFromProduct($mapping, $product, $profile);
        }

        $profile->debugLog((new Varien_Object(array('value' => $value)))->debug(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);

        if ($fieldDefinition->getHydrator() !== null) {
            $hydrator = $fieldDefinition->getHydrator();
            if (!$hydrator instanceof Billigerde_Connect_Model_Export_Hydrator_Interface) {
                Throw new Exception($fieldDefinition->getHydrating() . ' is not instance of Billigerde_Connect_Model_Export_Hydrator_Interface.' );
            }
            $hydrator->setProduct($product);
            if ($product->getCategory()) {
                $hydrator->setCategory($product->getCategory());
            }
            $hydrator->setFieldDefinition($fieldDefinition);
            $hydrator->setMappedValue($value);
            $hydrator->map();

            $value = $hydrator->getResult();
        } else {
            if ($fieldDefinition->getFormatter() !== null) {
                if (!$fieldDefinition->getFormatter() instanceof Billigerde_Connect_Model_Export_Formatter_Interface) {
                    Throw new Exception($fieldDefinition->getFormatting() . ' is not instance of Billigerde_Connect_Model_Export_Formatter_Interface.' );
                }
                $value = $fieldDefinition->getFormatter()->format($value);
            }
        }

        $value = new Varien_Object(array('value' => $value));

        $profile->debugLog($value->debug(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);

        mage::dispatchEvent('billigerde_connect_get_product_field_value', array('field' => $fieldDefinition, array('value' => $value)));

        $profile->debugLog($value->debug(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);

        return $value->getValue();
    }

    /**
     *
     * @param unknown $attributeId
     * @return Mage_Eav_Model_Attribute
     */
    protected function _getAttribute($attributeId)
    {
        if (!isset($this->_attributes[$attributeId])) {
            /** @var Mage_Eav_Model_Attribute $attribute */
            $attribute = mage::getModel('eav/entity_attribute');
            $attribute->load($attributeId);

            $this->_attributes[$attributeId] = $attribute;
        }
        return $this->_attributes[$attributeId];
    }

    protected function _getAttributeValueFromProduct($attributeId, Mage_Catalog_Model_Product $product, Billigerde_Connect_Model_Export_Profile $profile)
    {
        $attribute = $this->_getAttribute($attributeId);

        $entityType = $attribute->getEntityType()->getEntityTypeCode();

        $profile->debugLog($attribute->debug(), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);
        $profile->debugLog('Uses Source: ' . ($attribute->usesSource() ? 'true':'false'), Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);
        $profile->debugLog('EntityType: ' . $entityType, Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);

        if ($entityType == Mage_Catalog_Model_Product::ENTITY) {
            if ($attribute->usesSource()) {
                $value = $product->getAttributeText($attribute->getAttributeCode());
            } else {
                $value = $product->getDataUsingMethod($attribute->getAttributeCode());
            }
        } else if ($entityType == Mage_Catalog_Model_Category::ENTITY) {

            $category = $product->getCategory();

            if (!$category instanceof Mage_Catalog_Model_Category) {
                /** @var Mage_Catalog_Model_Resource_Category_Collection $categoryCollection */
                $categoryCollection = $product->getCategoryCollection();
                $categoryCollection->addAttributeToSort('level', 'desc');
                $categoryCollection->getSelect()->limit(1);
                $category = $categoryCollection->getFirstItem();
            }

            if ($category instanceof Mage_Catalog_Model_Category && $category->getId()) {
                $product->setCategory($category);
                $value = $this->_getAttributeValueFromCategory($attributeId, $category);
            } else {
                $value = '';
            }
        } else {
            Throw new Exception('Unsupported Attribute EntityType (' . $entityType . ')');
        }

        return $value;
    }

    protected function _getAttributeValueFromCategory($attributeId, Mage_Catalog_Model_Category $category)
    {
        $attribute = $this->_getAttribute($attributeId);
        $entityType = $attribute->getEntityType()->getEntityTypeCode();

        if ($entityType != Mage_Catalog_Model_Category::ENTITY) {
            Throw new Exception('Attribute (' . $attribute->getId() . ') is not EntityType ' . Mage_Catalog_Model_Category::ENTITY);
        }

        $value = $category->getData($attribute->getAttributeCode());
        return $value;
    }

    protected function _getGetterValueFromProduct($getter, Mage_Catalog_Model_Product $product, Billigerde_Connect_Model_Export_Profile $profile)
    {
        $getter = explode(Billigerde_Connect_Model_Adminhtml_System_Config_Source_Catalog_Entity_Mappings::SEPERATOR, $getter);

        $profile->debugLog($getter, Billigerde_Connect_Model_Export_Profile::DEBUG_LEVEL_DATA_MAP);

        $reference = $product;
        foreach ($getter as $get) {
            $reference = $reference->$get();
        }

        return $reference;
    }

}