<?php

class Swarming_RiseLms_Model_Observer_Catalog_ProductDeleteBefore
{
    /**
     * @var Swarming_RiseLms_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_RiseLms_Helper_Product
     */
    protected $_productHelper;

    /**
     * @var Swarming_RiseLms_Model_Service_UnlinkProduct
     */
    protected $_unlinkProductService;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_riselms/config_general');
        $this->_productHelper = Mage::helper('swarming_riselms/product');
        $this->_unlinkProductService = Mage::getModel('swarming_riselms/service_unlinkProduct');
    }

    /**
     * @return Mage_Core_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getData('product');

        if (!$this->_configGeneral->isEnabled($product->getStoreId())) {
            return;
        }

        if (!$this->_productHelper->isRiseLms($product)) {
            return;
        }

//        $product->getWebsiteIds()

        // TODO Which store should be used here?
        $result = $this->_unlinkProductService->unlinkProduct($product->getId(), $product->getStoreId());

        if ($result) {
            $this->_getSession()->addSuccess('Product has been successfully unlinked from Rise LMS.');
        } else {
            $this->_getSession()->addError('Product could not be successfully unlinked from Rise LMS.');
        }
    }
}
