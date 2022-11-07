<?php

class Billigerde_Connect_Model_System_Config_Profile_Debug
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach (Billigerde_Connect_Model_Export_Profile::getDebugLevels() as $value => $label) {
            $options[] = array('value' => $value, 'label'=> Mage::helper('billigerde_connect')->__($label));
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = array();

        foreach (Billigerde_Connect_Model_Export_Profile::getDebugLevels() as $value => $label) {
            $options[$value] = Mage::helper('billigerde_connect')->__($label);
        }

        return $options;
    }

}
