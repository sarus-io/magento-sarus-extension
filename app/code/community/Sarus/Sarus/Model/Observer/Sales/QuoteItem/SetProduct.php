<?php

class Sarus_Sarus_Model_Observer_Sales_QuoteItem_SetProduct
{
    /**
     * @param \Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getData('quote_item');

        /** @var \Mage_Catalog_Model_Product $product */
        $product = $observer->getData('product');

        if (!$quoteItem || !$product) {
            return;
        }

        $courseUuid = $product->getData(Sarus_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID);
        if ($courseUuid) {
            $quoteItem->setData(Sarus_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID, $courseUuid);
        }
    }
}
