<?php

class Swarming_SsoIdp_Block_Adminhtml_Config_Field_Url extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_getSCurrentScopeStore()->getUrl((string)$element->getValue(), array('_secure' => true));
    }

    /**
     * @return \Mage_Core_Model_Store
     */
    protected function _getSCurrentScopeStore()
    {
        $websiteCode = Mage::app()->getRequest()->getParam('website');
        return $websiteCode
            ? Mage::app()->getWebsite($websiteCode)->getDefaultStore()
            : Mage::app()->getDefaultStoreView();
    }

    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->setCanUseWebsiteValue(false);
        $element->setCanUseDefaultValue(false);
        return parent::render($element);
    }
}
