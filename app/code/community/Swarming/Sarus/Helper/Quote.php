<?php

class Swarming_Sarus_Helper_Quote
{
    /**
     * @var Swarming_Sarus_Helper_Product
     */
    protected $_productHelper;

    public function __construct()
    {
        $this->_productHelper = Mage::helper('swarming_sarus/product');
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function hasRiseProduct($quote)
    {
        if (!$quote->hasData('has_sarus_product')) {
            $quote->setData('has_sarus_product', $this->_hasRiseProduct($quote));
        }

        return $quote->getData('has_sarus_product');
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
            : $this->_productHelper->isSarus($quoteItem->getProduct());
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
            if ($this->_productHelper->isSarus($childItem->getProduct())) {
                $result = true;
                break;
            }
        }
        return $result;
    }
}
