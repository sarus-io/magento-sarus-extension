<?php

class Swarming_RiseLmsProduct_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
    * check if quote has riselms product
    * @return boolean
    */
    public function quoteHasRiseLmsProduct()
    {
        $quote = Mage::getSingleton('checkout/session')->getQuote();
        if (!$quote->hasData('has_riselms_product')) {
            $quote->setData('has_riselms_product', false);
            foreach ($quote->getAllVisibleItems() as $item) {
                if ($this->isQuoteItemRiseLmsProduct($item)) {
                    $quote->setData('has_riselms_product', true);
                    break;
                }
            }
        }

        return $quote->getData('has_riselms_product');
    }

    /**
     * @param Mage_Sales_Model_Quote_Item $item
     */
    public function isQuoteItemRiseLmsProduct($item)
    {
        return $item->getProduct()->isRiseLmsProduct();
    }

    public function getMaxPrice()
    {
        return (float)Mage::getStoreConfig('riselms_general/product/max_price');
    }

    public function getRiselmsMargin()
    {
        return (float)Mage::getStoreConfig('riselms_general/product/riselms_margin');
    }
}
