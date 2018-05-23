<?php

class Sarus_Sarus_Model_Observer_Checkout_AllowGuest
{
    /**
     * @var Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Sarus_Sarus_Helper_Quote
     */
    protected $_quoteHelper;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        $this->_quoteHelper = Mage::helper('sarus_sarus/quote');
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

        if ($this->_quoteHelper->hasSarusProduct($quote)) {
            $result->setIsAllowed(false);
        }
    }
}
