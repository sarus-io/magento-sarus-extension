<?php

class Sarus_Sarus_Model_Observer_Sales_QuoteItem_SetQtyAfter
{
    /**
     * @var \Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var \Sarus_Sarus_Helper_Quote
     */
    protected $_quoteHelper;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        $this->_quoteHelper = Mage::helper('sarus_sarus/quote');
    }

    /**
     * Make sure the sarus product quantity is 1
     *
     * @param \Varien_Event_Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        /** @var \Mage_Sales_Model_Quote_Item $quoteItem */
        $quoteItem = $observer->getData('item');

        if (!$this->_configGeneral->isEnabled($quoteItem->getStoreId())) {
            return;
        }

        if ($this->_quoteHelper->hasQuoteItemSarusProduct($quoteItem)) {
            $quoteItem->setData('qty', 1);
        }
    }
}
