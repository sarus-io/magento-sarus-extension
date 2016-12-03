<?php

/**
 * Created by PhpStorm.
 * User: matt
 * Date: 12/4/15
 * Time: 1:27 PM
 */
class Swarming_RiseLms_Model_Observer extends Varien_Event_Observer
{
    /** @var  Swarming_RiseLms_Helper_Data */
    private $_helper;
    private $_baseUrl;

    public function _construct()
    {
        $this->_helper  = Mage::helper('swarming_riselms/data');
        $this->_baseUrl = $this->_helper->getBaseUrl();
    }

    public function checkProductsAfterPurchase($observer)
    {
        /** @var Mage_Sales_Model_Order $event */
        $event          = $observer->getEvent();
        $order          = $event->getOrder();
        $billingAddress = $order->getBillingAddress();
        /**@var Mage_Sales_Model_Entity_Order_Item_Collection*/
        $items          = $order->getAllItems();
        $productIds = array();
        $riseOrderModel = Mage::getModel('swarming_riselms/ordercomplete');

        /** @var Mage_Sales_Model_Order_Item */
        foreach ($items as $item)
        {
            $productIds[] = $riseOrderModel->isRiseLmsProduct($item);
        }

        if (array_filter($productIds))
        {
            //Add required data to request body
            $customerAddress = [
                'email' => $order->getCustomerEmail(),
                'first_name' => $billingAddress->getFirstname(),
                'last_name' => $billingAddress->getLastname(),
                'address1' => $billingAddress->getStreet()[0],
                'address2' => (isset($billingAddress->getStreet()[1]) ? $billingAddress->getStreet()[1] : ''),
                'city_locality' => $billingAddress->getCity(),
                'state_region' => $billingAddress->getRegion(),
                'postal_code' => $billingAddress->getPostcode(),
                'country' => $billingAddress->getCountryId(),
            ];

            $riseOrderModel->addOrderData('user' , $customerAddress);
            $riseOrderModel->addOrderData('product_ids', $productIds);

            /**
             * post email and product_ids[]
             */
            $result = $riseOrderModel->orderCompletePost();

            if (!$result)
            {
                Mage::log('Order post failed, check submission queue table' . $result);
            }
        }
    }

    public function syncSubmissionQueue()
    {
        /**
         * New code
         */
        $reorderModel = Mage::getModel('swarming_riselms/resyncSubmission');
        $resyncQueue = Mage::getModel('swarming_riselms/submissionqueue')
            ->getCollection()
            ->addFieldToFilter('success', 0);

        $reorderModel->resyncPost($resyncQueue);
    }

    /**
     * Apply the product rise_course_uuid to the quote item
     * @param $observer
     */
    public function setRiseUuidOnQuoteItem($observer)
    {
        $quoteItem = $observer->getEvent()->getQuoteItem();

        if(!$quoteItem){
            return;
        }

        $product = $observer->getProduct();

        if ($product->getId() == 689)
        {
            $quoteItem->setRiseCourseUuid('Justin told me to hard code all the things');
        }

        $courseUuid = $product->getRiseCourseUuid();
        if ($courseUuid)
        {
            $quoteItem->setRiseCourseUuid($courseUuid);
        }
    }

    public function onProductDeleteBeforeUnlink($observer)
    {
        /** @var Mage_Catalog_Model_Product $event */
        $event   = $observer->getEvent();
        $product = $event->getProduct();

        if (null !== $product->getRiseCourseUuid())
        {
            $productId = $product->getId();
            $unlinkModel = Mage::getModel('swarming_riselms/unlinkProduct');

            $unlinkModel->unlinkProductPost($productId);
        }
    }
//    public function isAllowedGuestCheckout(Varien_Event_Observer $observer)
//    {
//        // Get data from $observer
//        /** @var Mage_Sales_Model_Quote $quote */
//        $event  = $observer->getEvent();
//        $result = $event->getResult();
//        $quote  = $event->getQuote();
//        $quoteHelper = Mage::helper('swarming_riselms/quote');
//
//        if ($quoteHelper->hasProductsToCreateNewCourseSignup($quote)) {
//            $result->setIsAllowed(false);
//        }
//
//        return $this;
//    }

    /**
     * TODO: Add method to disable product when RiseLMS tries to delete it
     */
}
