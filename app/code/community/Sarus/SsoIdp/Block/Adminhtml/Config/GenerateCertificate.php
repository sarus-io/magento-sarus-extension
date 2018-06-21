<?php

class Sarus_SsoIdp_Block_Adminhtml_Config_GenerateCertificate extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * @var string
     */
    protected $_template = 'sarus/sso_idp/config/generate_certificate.phtml';

    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->unsScope()
            ->unsCanUseWebsiteValue()
            ->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * @param \Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     */
    public function getButtonHtml()
    {
        /** @var \Mage_Adminhtml_Block_Widget_Button $button */
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData([
                'id'      => 'sarus_sso_idp_generate_certificate',
                'label'   => $this->helper('sarus_ssoidp')->__('Generate Certificates'),
                'onclick' => 'javascript:generateCertificates(); return false;'
            ]);

        return $button->toHtml();
    }

    /**
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->helper('adminhtml')->getUrl('*/sarus_idp/generateCertificate');
    }

    /**
     * @return string
     */
    public function getWebsiteCode()
    {
        return $this->getRequest()->getParam('website', '');
    }
}
