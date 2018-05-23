<?php

class Sarus_Sarus_Helper_Quote
{
    /**
     * @var Sarus_Sarus_Helper_Product
     */
    protected $_productHelper;

    public function __construct()
    {
        $this->_productHelper = Mage::helper('sarus_sarus/product');
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function hasSarusProduct($quote)
    {
        if (!$quote->hasData('has_sarus_product')) {
            $quote->setData('has_sarus_product', $this->_hasSarusProduct($quote));
        }

        return $quote->getData('has_sarus_product');
    }

    /**
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    protected function _hasSarusProduct($quote)
    {
        $result = false;

        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            if ($this->hasQuoteItemSarusProduct($quoteItem)) {
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
    public function hasQuoteItemSarusProduct($quoteItem)
    {
        return $quoteItem->getChildren()
            ? $this->_hasChildItemsSarusProduct($quoteItem)
            : $this->_productHelper->isSarus($quoteItem->getProduct());
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $quoteItem
     * @return bool
     */
    protected function _hasChildItemsSarusProduct($quoteItem)
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
