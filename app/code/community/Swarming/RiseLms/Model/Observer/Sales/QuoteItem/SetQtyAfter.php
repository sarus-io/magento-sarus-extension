<?php

class Swarming_RiseLms_Model_Observer_Sales_QuoteItem_SetQtyAfter
{
    /**
     * Make sure the riselms product quantity is 1
     *
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        /** @var Mage_Sales_Model_Quote_Item $item */
        $item = $observer->getItem();
        if ($item->getProduct()->isRiseLmsProduct()) {
            $item->setData('qty', 1);
            //@todo add message to user if they try to add more then 1 RiseLMS Prod.

        }
    }
}
