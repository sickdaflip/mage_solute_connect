<?php
/**
 *
 * @author Steffen Kieckhäven <steffen.kieckhaeven@shopping.de>
 *
 */
class Billigerde_Connect_Block_Adminhtml_System_Config_Form_Field_Select extends Mage_Adminhtml_Block_Html_Select
{

    /**
     * @param array $config
     */
    public function prepareConfig($config)
    {
        try {
            if (isset($config['source_model'])) {
                $sourceModel = mage::getModel($config['source_model']);
                if ($sourceModel) {
                    $this->setOptions($sourceModel->toArray());
                }
            } else if (isset($config['options']) && is_array($config['options'])) {
                $this->setOptions($config['options']);
            }

            if (isset($config['label'])) {

            }

            // @todo add all <fields>-Options
            // @todo implement all frontend_type Values

        } catch (Exception $e) {
            try {
                throw new RuntimeException('prepareConfig failed (' . $e->getMessage() . ')',null, $e);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }

        return $this;
    }


    protected function _toHtml()
    {
        $this->setName($this->getInputName());
        $this->setId('#{_id}');

        #$this->setOptions($this->getAllOptions());

        return parent::_toHtml();
    }


}