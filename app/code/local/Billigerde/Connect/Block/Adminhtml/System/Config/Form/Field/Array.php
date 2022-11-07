<?php
/**
 *
 * @author Steffen Kieckhäven <steffen.kieckhaeven@shopping.de>
 *
 */
class Billigerde_Connect_Block_Adminhtml_System_Config_Form_Field_Array extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    public function __construct()
    {
        // no parent construct, why ever - error
        $this->setTemplate('billigerde_connect/system/config/form/field/array.phtml');
    }

    /**
     * @return Billigerde_Connect_Block_Adminhtml_System_Config_Form_Field_Select
     */
    public function getNewSelectBlock()
    {
        return Mage::app()->getLayout()->createBlock('billigerde_connect/adminhtml_system_config_form_field_select');
    }

    public function addColumn($name, $params)
    {
        parent::addColumn($name, $params);
        $this->_columns[$name]['info'] = isset($params['info']) ? $params['info'] : '';
        return $this;
    }


}