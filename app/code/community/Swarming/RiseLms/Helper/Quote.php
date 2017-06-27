<?php

class Swarming_RiseLms_Helper_Quote
{
    /**
     * @var Swarming_RiseLms_Helper_Product
     */
    protected $_productHelper;

    public function __construct()
    {
        $this->_productHelper = Mage::helper('swarming_riselms/product');
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function hasRiseProduct($quote)
    {
        if (!$quote->hasData('has_riselms_product')) {
            $quote->setData('has_riselms_product', $this->_hasRiseProduct($quote));
        }

        return $quote->getData('has_riselms_product');
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    protected function _hasRiseProduct($quote)
    {
        $result = false;

        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        foreach ($quote->getAllItems() as $quoteItem) {
            if ($this->_productHelper->isRiseLms($quoteItem->getProduct())) {
                $result = true;
                break;
            }
        }

        return $result;
    }
}
