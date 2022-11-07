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
class Billigerde_Connect_Model_Adminhtml_System_Config_Source_Export_Field_Hydrators
{

    protected $_hydrators = array(
        '0' => '',
        'billigerde_connect/export_hydrator_catalog_category_tree' => 'CategoryTreeString',
        'billigerde_connect/export_hydrator_catalog_product_image' => 'ProductMediaUrl',
    );
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
        $hydrators = new Varien_Object($this->_hydrators);

        mage::dispatchEvent('billigerde_connect_fetch_hydrators', array('hydrators' => $hydrators));

        $return = array();

        foreach ($hydrators->getData() as $key => $value) {
                $return[$key] = $value;
        }

        return $return;

    }
}