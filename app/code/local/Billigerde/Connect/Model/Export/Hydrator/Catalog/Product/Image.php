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
class Billigerde_Connect_Model_Export_Hydrator_Catalog_Product_Image extends Billigerde_Connect_Model_Export_Hydrator_Abstract
{

    public function mapValue()
    {

        $this->setResult((string)Mage::helper('catalog/image')->init($this->getProduct(), $this->getFieldDefinition()->getField()));

        return $this;
    }


}