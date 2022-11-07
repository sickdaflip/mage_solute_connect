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
class Billigerde_Connect_Model_Adminhtml_System_Config_Source_Export_Field_Filter_Visibility
{
    public function toOptionArray()
    {
        $options = array();

        /** @var $productVisibility Mage_Catalog_Model_Product_Visibility */
        $productVisibility = mage::getSingleton('catalog/product_visibility');
        foreach ($productVisibility->getOptionArray() as $key => $value) {
            $options[$key] = array('value' => $key, 'label' => $value, 'title' => $value, 'style' => '' );
        }

        return $options;
    }
}