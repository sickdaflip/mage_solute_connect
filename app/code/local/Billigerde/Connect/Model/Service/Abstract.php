<?php
/**
 *
 * @author Steffen Kieckhaeven <steffen.kieckhaeven@shopping.de>
 *
 */
abstract class Billigerde_Connect_Model_Service_Abstract extends Varien_Object
{
    /**
     * @var Mage_Core_Model_App_Emulation
     */
    protected $_appEmulation = null;

    /**
     * @var Varien_Object
     */
    protected $_appEmulationInfoBeforeStart = null;

    /**
     * @return $this
     */
    public function startEmulation(Mage_Core_Model_Store $store)
    {
        $this->_appEmulationInfoBeforeStart = $this->getAppEmulation()->startEnvironmentEmulation($store->getId());
        return $this;
    }

    /**
     * @return $this
     */
    public function stopEmulation()
    {
        $this->getAppEmulation()->stopEnvironmentEmulation($this->getAppEmulationInfoBeforeStart());

        return $this;
    }

    /**
     * @return Varien_Object
     */
    public function getAppEmulationInfoBeforeStart()
    {
        return $this->_appEmulationInfoBeforeStart;
    }

    /**
     *
     *
     * @param Mage_Core_Model_App_Emulation $value
     */
    public function setAppEmulation(Mage_Core_Model_App_Emulation $value)
    {
        $this->_appEmulation = $value;
    }

    /**
     * @return Mage_Core_Model_Abstract|Mage_Core_Model_App_Emulation
     */
    public function getAppEmulation()
    {
        if (!$this->_appEmulation instanceof Mage_Core_Model_App_Emulation) {
            $this->_appEmulation = Mage::getSingleton('core/app_emulation');
        }
        return $this->_appEmulation;
    }


}