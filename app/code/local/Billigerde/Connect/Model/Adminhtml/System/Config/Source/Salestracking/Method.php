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
class Billigerde_Connect_Model_Adminhtml_System_Config_Source_Salestracking_Method
{
    const METHOD_NONE = 'none';
    const METHOD_INCLUDE_ORDER_ITEMS = 'include_order_items';
    const METHOD_EXCLUDE_ORDER_ITEMS = 'exclude_order_items';

    public function toOptionArray()
    {
        $helper = mage::helper('billigerde_connect');

        return array(
            self::METHOD_NONE => $helper->__('source_label_' . self::METHOD_NONE),
            self::METHOD_INCLUDE_ORDER_ITEMS => $helper->__('source_label_' . self::METHOD_INCLUDE_ORDER_ITEMS),
            self::METHOD_EXCLUDE_ORDER_ITEMS => $helper->__('source_label_' . self::METHOD_EXCLUDE_ORDER_ITEMS),
        );
    }
}