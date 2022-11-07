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
class Billigerde_Connect_Block_Frontend_Checkout_Success_Tracking extends Mage_Core_Block_Template
{
    public function isTrackingEnabled()
    {
        return mage::getStoreConfig('billigerde_connect_setting/sales_tracking/method') !== Billigerde_Connect_Model_Adminhtml_System_Config_Source_Salestracking_Method::METHOD_NONE;
    }


    public function getTrackingUrl()
    {
        $method = mage::getStoreConfig('billigerde_connect_setting/sales_tracking/method');

        $queryData = array(
            'shop_id' => mage::getStoreConfig('billigerde_connect_setting/general/shop_id'),
            'oid' => $this->getOrder()->getId(),
        );

        switch ($method) {
            case Billigerde_Connect_Model_Adminhtml_System_Config_Source_Salestracking_Method::METHOD_EXCLUDE_ORDER_ITEMS:
                $queryData = array_merge($queryData, $this->getOrderTotalValue());
                break;
            case Billigerde_Connect_Model_Adminhtml_System_Config_Source_Salestracking_Method::METHOD_INCLUDE_ORDER_ITEMS:
                $queryData = array_merge($queryData, $this->getOrderItems());
                break;
            default: break;
        }

        $url = 'https://billiger.de/sale?' . http_build_query($queryData);

        return $url;
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        /** @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());

        return $order;
    }

    public function getOrderItems()
    {
        $orderItems = array();

        $counter = 1;
        /** @var $orderItem Mage_Sales_Model_Order_Item */
        foreach ($this->getOrder()->getAllVisibleItems() as $orderItem) {
            $orderItems['aid_' . $counter] = $orderItem->getSku();
            $orderItems['name_' . $counter] = $orderItem->getName();
            $orderItems['cnt_' . $counter] = $orderItem->getQtyOrdered();
            $orderItems['val_' . $counter] = $orderItem->getBasePrice();

            $counter++;
        }

        return $orderItems;
    }

    public function getOrderTotalValue()
    {
        return array('val' => $this->getOrder()->getBaseSubtotal());
    }

}