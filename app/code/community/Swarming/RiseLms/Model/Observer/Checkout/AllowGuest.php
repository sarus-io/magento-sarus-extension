<?php

class Swarming_RiseLms_Model_Observer_Checkout_AllowGuest
{
    /**
     * @var Swarming_RiseLms_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_RiseLms_Helper_Quote
     */
    protected $_quoteHelper;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_riselms/config_general');
        $this->_quoteHelper = Mage::helper('swarming_riselms/quote');
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Quote $quote */
        $quote  = $observer->getData('quote');

        $result = $observer->getData('result');

        if (!$this->_configGeneral->isEnabled($quote->getStoreId())) {
            return;
        }

        if ($this->_quoteHelper->hasRiseProduct($quote)) {
            $result->setIsAllowed(false);
        }
    }
}
