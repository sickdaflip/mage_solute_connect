<?php
/**
 *
 * @category   Billigerde
 * @package    Billigerde_Connect
 * @subpackage
 * @author   Steffen Kieckhäven  <steffen.kieckhaeven@shopping.de>
 *
 */
class Billigerde_Connect_Block_Adminhtml_System_Config_Form_Field_Export_Profile_Config extends Billigerde_Connect_Block_Adminhtml_System_Config_Form_Field_Array
{

    public function _prepareToRender()
    {

        foreach (mage::getModel('billigerde_connect/adminhtml_system_config_source_export_profile_config_columns')->getColumns() as $column => $columnConfig) {
            if (isset ($columnConfig['frontend_label'])) {
                $frontendLabel= $columnConfig['frontend_label'];
            } else {
            	$frontendLabel = $column;
            }

            if (isset($columnConfig['frontend_type']) && $columnConfig['frontend_type'] == 'select') {
                $renderer = $this->getNewSelectBlock();
                $renderer->prepareConfig($columnConfig);
            } else {
                $renderer = false;
            }

            $this->addColumn($column, array(
                'label' => $frontendLabel,
                'style' => 'width:120px',
                'renderer' => $renderer !== null ? $renderer : false,
            ));

        }

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add Csv Column');
        parent::__construct();
    }

}