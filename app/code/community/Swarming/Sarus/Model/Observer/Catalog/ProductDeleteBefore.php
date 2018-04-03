<?php

class Swarming_Sarus_Model_Observer_Catalog_ProductDeleteBefore
{
    /**
     * @var Swarming_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_Sarus_Helper_Product
     */
    protected $_productHelper;

    /**
     * @var Swarming_Sarus_Model_Service_UnlinkProduct
     */
    protected $_unlinkProductService;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_sarus/config_general');
        $this->_productHelper = Mage::helper('swarming_sarus/product');
        $this->_unlinkProductService = Mage::getModel('swarming_sarus/service_unlinkProduct');
        $this->_app = Mage::app();
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

        if (!$this->_productHelper->isSarus($product)) {
            return;
        }

        foreach ($product->getWebsiteIds() as $websiteId) {
            $storeId = $this->_app->getWebsite($websiteId)->getDefaultStore()->getId();
            if (!$this->_configGeneral->isEnabled($storeId)) {
                continue;
            }

            $this->_unlinkProduct($product, $storeId);
        }
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param int $storeId
     * @return void
     */
    protected function _unlinkProduct($product, $storeId)
    {
        $result = $this->_unlinkProductService->unlinkProduct($product->getId(), $storeId); // TODO Remove after BrainMD migration
        if ($result) {
            $this->_getSession()->addSuccess('Product has been successfully unlinked from Rise LMS.');
        }

        $result = $this->_unlinkProductService->unlinkProduct($product->getData(Swarming_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID), $storeId);
        if ($result) {
            $this->_getSession()->addSuccess('Product has been successfully unlinked from Rise LMS.');
        }
    }
}
