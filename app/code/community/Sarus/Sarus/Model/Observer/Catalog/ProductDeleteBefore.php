<?php

use Sarus\Request\Product\Unlink as SarusUnlink;

class Sarus_Sarus_Model_Observer_Catalog_ProductDeleteBefore
{
    /**
     * @var \Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var \Sarus_Sarus_Helper_Product
     */
    protected $_productHelper;

    /**
     * @var \Sarus_Sarus_Model_Platform
     */
    protected $_platform;

    /**
     * @var \Mage_Core_Model_App
     */
    protected $_app;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        $this->_productHelper = Mage::helper('sarus_sarus/product');
        $this->_platform = Mage::getModel('sarus_sarus/platform');
        $this->_app = Mage::app();
    }

    /**
     * @return \Mage_Core_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * @param \Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var \Mage_Catalog_Model_Product $product */
        $product = $observer->getData('product');

        if (!$this->_productHelper->isSarus($product)) {
            return;
        }

        foreach ($product->getWebsiteIds() as $websiteId) {
            $website = $this->_app->getWebsite($websiteId);
            if ($website->getCode() === 'admin') {
                continue;
            }

            $storeId = $website->getDefaultStore()->getStoreId();
            if (!$this->_configGeneral->isEnabled($storeId)) {
                continue;
            }

            if ($this->unlinkProduct($product, $storeId)) {
                $this->_getSession()->addSuccess('Product has been successfully unlinked from Sarus.');
                break;
            }
        }
    }

    /**
     * @param \Mage_Catalog_Model_Product $product
     * @param int $storeId
     * @return bool
     */
    protected function unlinkProduct($product, $storeId)
    {
        $sarusRequest = new SarusUnlink($product->getData(Sarus_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID));
        try {
            $this->_platform->sendRequest($sarusRequest, $storeId);
            $result = true;
        } catch (\Exception $e) {
            Mage::logException($e);
            $result = false;
        }
        return $result;
    }
}
