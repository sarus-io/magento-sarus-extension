<?php

class Swarming_RiseLmsProduct_Model_Observer
{
    // make sure the riselms product quantity is 1
    public function salesQuoteItemQtySetAfter($observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        $item = $observer->getItem();
        if ($item->getProduct()->isRiseLmsProduct()) {
            $item->setData('qty', 1);
            //@todo add message to user if they try to add more then 1 RiseLMS Prod. 

        }
    }
}