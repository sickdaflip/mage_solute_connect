<?php
/**
 *
 * @author Steffen Kieckhäven <steffen.kieckhaeven@shopping.de>
 *
 */
class Billigerde_Connect_Model_Adminhtml_System_Config_Source_Export_Profile_Config_Columns
{
    protected $_columns = array(
        'field' =>  array(
            'frontend_label' => 'Field',
        ),
        'mapping' => array(
            'frontend_label' => 'Mapping',
            'frontend_type' => 'select',
            'source_model' => 'billigerde_connect/adminhtml_system_config_source_catalog_entity_mappings',
        ),
        'hydrating' => array(
            'frontend_label' => 'Hydrating',
            'frontend_type' => 'select',
            'source_model' => 'billigerde_connect/adminhtml_system_config_source_export_field_hydrators',
        ),
        'formatting' => array(
            'frontend_label' => 'Formatting',
            'frontend_type' => 'select',
            'source_model' => 'billigerde_connect/adminhtml_system_config_source_export_field_formatters',
        ),
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
        $return = array();

        foreach ($this->_columns as $column => $columnConfig) {
            if (isset ($columnConfig['frontend_label'])) {
                $return[$column] = $columnConfig['frontend_label'];
            } else {
            	$return[$column] = $column;
            }
        }
        return $return;
    }

    /**
     *
     * @return multitype:multitype:string
     */
    public function getColumns()
    {
    	return $this->_columns;
    }

}