<?php

class Swarming_RiseLms_Model_Observer_Sales_QuoteItem_SetQtyAfter
{
    /**
     * @var Swarming_RiseLms_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_RiseLms_Helper_Product
     */
    protected $_productHelper;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_riselms/config_general');
        $this->_productHelper = Mage::helper('swarming_riselms/product');
    }

    /**
     * Make sure the riselms product quantity is 1
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        $item = $observer->getData('item');

        if (!$this->_configGeneral->isEnabled($item->getStoreId())) {
            return;
        }

        if ($this->_productHelper->isRiseLms($item->getProduct())) {
            // TODO add message to user if they try to add more then 1 RiseLMS Prod.
            $item->setData('qty', 1);
        }
    }
}
