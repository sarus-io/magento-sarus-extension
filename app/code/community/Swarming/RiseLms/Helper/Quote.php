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
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($this->hasQuoteItemRiseProduct($quoteItem)) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     * @return bool
     */
    public function hasQuoteItemRiseProduct($quoteItem)
    {
        return $quoteItem->getChildren()
            ? $this->_hasChildItemsRiseProduct($quoteItem)
            : $this->_productHelper->isRiseLms($quoteItem->getProduct());
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     * @return bool
     */
    protected function _hasChildItemsRiseProduct($quoteItem)
    {
        $result = false;
        /** @var Mage_Sales_Model_Quote_Item $childItem */
        foreach ($quoteItem->getChildren() as $childItem) {
            if ($this->_productHelper->isRiseLms($childItem->getProduct())) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
