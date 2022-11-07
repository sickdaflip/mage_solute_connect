<?php
/**
 *
 * @author Steffen Kieckhäven <steffen.kieckhaeven@shopping.de>
 *
 */
class Billigerde_Connect_Model_Adminhtml_System_Config_Backend_Export_Profile_Config extends Mage_Adminhtml_Model_System_Config_Backend_Serialized_Array
{
    protected function _beforeSave()
    {
        $value = $this->getValue();

        if (!is_array($value)) {
            throw new Exception('Value is not Array.');
        }

        $valid = true;
        foreach ($value as $key => $row) {
            if ($key == '__empty') {
            	continue;
            }
            // TODO add checks
        }

        if (!$valid) {
            throw new Exception('Value is not Valid.');
        }

    	return parent::_beforeSave();
    }

    protected function _afterLoad()
    {
        $value = $this->getValue();
        // Mage 1.9.3.2 fix serialization error while not casted to string
        if ($value instanceof Mage_Core_Model_Config_Element) {
            $this->setValue((string)$value);
        }

        parent::_afterLoad();
    }


}
