<?php

class Sarus_SsoIdp_Block_Adminhtml_Config_Field_Url extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_createUrlModel()->getUrl((string)$element->getValue(), ['_secure' => true, '_nosid' => true]);
    }

    /**
     * @return \Mage_Core_Model_Url
     */
    protected function _createUrlModel()
    {
        return Mage::getModel('core/url')->setStore($this->_getCurrentScopeStore());
    }

    /**
     * @return \Mage_Core_Model_Store
     */
    protected function _getCurrentScopeStore()
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
