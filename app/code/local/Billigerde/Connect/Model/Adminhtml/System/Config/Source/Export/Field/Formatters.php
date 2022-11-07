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
class Billigerde_Connect_Model_Adminhtml_System_Config_Source_Export_Field_Formatters
{

    protected $_formatters = array(
        'billigerde_connect/export_formatter_text' => 'Text',
        'billigerde_connect/export_formatter_price' => 'Price',
        'billigerde_connect/export_formatter_float' => 'Float',
        'billigerde_connect/export_formatter_integer' => 'Integer',
    );

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = array('0' => '');

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
        $formatters = new Varien_Object($this->_formatters);

        mage::dispatchEvent('billigerde_connect_fetch_formatters', array('formatters' => $formatters));

        $return = array();

        foreach ($formatters->getData() as $key => $value) {
                $return[$key] = $value;
        }

        return $return;
    }
}