<?php

class Swarming_RiseLms_Model_Observer_Sales_QuoteItem_SetProduct
{
    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getData('quote_item');

        /** @var Mage_Catalog_Model_Product $product */
        $product = $observer->getData('product');

        if(!$quoteItem || !$product){
            return;
        }

        $courseUuid = $product->getRiseCourseUuid();
        if ($courseUuid) {
            $quoteItem->setRiseCourseUuid($courseUuid);
        }
    }
}
