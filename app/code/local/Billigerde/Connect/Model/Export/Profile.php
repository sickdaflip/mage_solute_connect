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
class Billigerde_Connect_Model_Export_Profile extends Mage_Core_Model_Abstract
{
    const DEBUG_LEVEL_NO        = 0;
    const DEBUG_LEVEL_FLOW      = 1;
    const DEBUG_LEVEL_DATA      = 2;
    const DEBUG_LEVEL_DATA_MAP  = 3;
    const DEBUG_LEVEL_FULL      = 4;

    protected $_writer = null;
    protected $_fields = array();

    public static function getDebugLevels()
    {
        return array(
            self::DEBUG_LEVEL_NO        => 'no_debug',
            self::DEBUG_LEVEL_FLOW      => 'flow_debug',
            self::DEBUG_LEVEL_DATA      => 'data_debug',
            self::DEBUG_LEVEL_DATA_MAP  => 'data_map_debug',
            self::DEBUG_LEVEL_FULL      => 'full_debug',
        );
    }

    public function getWriter()
    {
        return $this->_writer;
    }

    public function setWriter(Billigerde_Connect_Model_Export_Writer_Abstract $writer)
    {
        $this->_writer = $writer;
        return $this;
    }

    public function isActiveForStore(Mage_Core_Model_Store $store)
    {
        if (mage::getStoreConfigFlag('billigerde_connect_setting/export/is_active', $store)) {
            return true;
        } else {
            return false;
        }
    }


    public function getDebug()
    {
        return mage::getStoreConfig('billigerde_connect_setting/export/do_debug');
    }

    public function debugLog($value, $level = 1)
    {
        if ($this->getDebug() && $level <= $this->getDebug()) {
            mage::log($value, Zend_Log::DEBUG, 'billigerde_connect_debug_' . $this->getFileName() . '_' . mage::app()->getStore()->getId() . '.log');
        }
        return $this;
    }

    public function getFields()
    {
        if (!isset($this->_fields[mage::app()->getStore()->getId()])) {
            $configFields = mage::getStoreConfig('billigerde_connect_setting/export/profile_config');

            $fields = array();
            $modelsByDef = array();

            foreach ($configFields as $configField) {
                if ($configField['mapping'] === '0') {
                    continue;
                }

                $field = new Varien_Object($configField);

                if ($field->getFormatting() !== null && $field->getFormatting() != '0' && $field->getFormatting() !== '') {
                    if (isset($modelsByDef[$field->getFormatting()])) {
                        $field->setFormatter($modelsByDef[$field->getFormatting()]);
                    } else {
                        $modelByDef = mage::getModel($field->getFormatting());
                        if ($modelByDef instanceof Billigerde_Connect_Model_Export_Formatter_Interface) {
                            $modelsByDef[$field->getFormatting()] = $modelByDef;
                            $field->setFormatter($modelByDef);
                        }
                    }
                }

                if ($field->getHydrating() !== null && $field->getHydrating() != '0' && $field->getHydrating() !== '') {
                    if (isset($modelsByDef[$field->getHydrating()])) {
                        $field->setHydrator($modelsByDef[$field->getHydrating()]);
                    } else {
                        $modelByDef = mage::getModel($field->getHydrating());
                        if ($modelByDef instanceof Billigerde_Connect_Model_Export_Hydrator_Interface) {
                            $modelsByDef[$field->getHydrating()] = $modelByDef;
                            $field->setHydrator($modelByDef);
                        }
                    }
                }

                mage::dispatchEvent('billigerde_connect_map_field', array('field' => $field));

                $fields[$field->getField()] = $field;
            }

            $this->_fields[Mage::app()->getStore()->getId()] = $fields;
        }
        return $this->_fields[Mage::app()->getStore()->getId()];
    }

    public function applyProductCollectionFilter(Mage_Catalog_Model_Resource_Product_Collection $collection)
    {

        $collection->addAttributeToFilter('type_id', array('in' => array('simple')));

        $categoryExcludeFilter = mage::getStoreConfig('billigerde_connect_setting/export/category_filter_exclude');
        $categoryIncludeFilter = mage::getStoreConfig('billigerde_connect_setting/export/category_filter_include');

        if ($categoryExcludeFilter !== '' || $categoryIncludeFilter !== '') {
            $collection->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left');
            $collection->getSelect()->group('e.entity_id');
        }

        if ($categoryExcludeFilter !== '') {
            $collection->addAttributeToFilter('category_id', array('nin' => explode(',',$categoryExcludeFilter)));
        }

        if ($categoryIncludeFilter !== '') {
            $collection->addAttributeToFilter('category_id', array('in' => explode(',', $categoryIncludeFilter)));
        }

        $visibilityFilter = mage::getStoreConfig('billigerde_connect_setting/export/visibility_filter');
        if ($visibilityFilter !== '') {
            $collection->setVisibility(explode(',', $visibilityFilter));
        }

        $statusFilter = mage::getStoreConfig('billigerde_connect_setting/export/include_disabled_products');
        if ($statusFilter) {
            $collection->addAttributeToFilter('status', array('in' => array(Mage_Catalog_Model_Product_Status::STATUS_DISABLED,Mage_Catalog_Model_Product_Status::STATUS_ENABLED)));
        } else {
            $collection->addAttributeToFilter('status', array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED));
        }

        $stockStatusFilter = mage::getStoreConfig('billigerde_connect_setting/export/include_not_in_stock_products');
        if (!$stockStatusFilter) {
            Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
        }

        mage::dispatchEvent('billigerde_connect_apply_product_collection_filter', array('collection' => $collection));
        return $this;
    }

    public function getDelimiter()
    {
        return mage::getStoreConfig('billigerde_connect_setting/export/csv_delimiter');
    }

    public function getEnclosure()
    {
        return mage::getStoreConfig('billigerde_connect_setting/export/csv_enclosure');
    }

    public function getFileName()
    {
        if (mage::getStoreConfig('billigerde_connect_setting/export/csv_file_name') == mage::getStoreConfig('billigerde_connect_setting/export/csv_file_name', 0)) {
            // Fix overwriting Store Exports when default Settings used by appending store_id to Filename
            $filename = mage::getStoreConfig('billigerde_connect_setting/export/csv_file_name');
            if (strpos($filename, '.csv') !== false) {
                $filename = str_replace('.csv', '', $filename);
            }
            return $filename . '_store_' . mage::app()->getStore()->getId() . '.csv';
        } else {
            return mage::getStoreConfig('billigerde_connect_setting/export/csv_file_name');
        }
    }

    public function getFilePath()
    {
        return mage::getStoreConfig('billigerde_connect_setting/export/csv_file_path');
    }

}