<?php

class Sarus_Sarus_Block_Checkout_Success extends Mage_Core_Block_Template
{
    /**
     * @var \Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var \Sarus_Sarus_Helper_Order
     */
    protected $_orderHelper;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        $this->_orderHelper = Mage::helper('sarus_sarus/order');
        parent::_construct();
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * @return bool
     */
    public function hasOrderSarusProduct()
    {
        $order = $this->_getCheckoutSession()->getLastRealOrder();
        return !empty($this->_orderHelper->getSarusProductUuids($order));
    }

    /**
     * @return bool
     */
    public function isMyCoursesEnabled()
    {
        return $this->_configGeneral->isMyCoursesEnabled();
    }

    /**
     * @return string
     */
    public function getSarusProductsUrl()
    {
        return $this->getUrl('sarus_sarus/list');
    }

    /**
     * @return string
     */
    public function getSarusStaticBlockHtml()
    {
        return $this->getLayout()->createBlock('cms/block')->setBlockId('sarus_success_block')->toHtml();
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->hasOrderSarusProduct() ? parent::_toHtml() : '';
    }
}
