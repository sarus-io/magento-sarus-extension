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
                'identity_provider_id' => $order->getCustomerId(),
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
    /**
     * Refund process
     * used for event: sales_order_creditmemo_save_after
     *
     * @param Varien_Event_Observer $observer
     * @return Swarming_Riselms_Model_Observer
     */
    public function creditmemoSaveAfter(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        /**@var Mage_Sales_Model_Entity_Order_Item_Collection*/
        $items          = $order->getAllItems();
        $productIds     = array();
        $riseOrderModel = Mage::getModel('swarming_riselms/creditmemo');

        /** @var Mage_Sales_Model_Order_Item */
        foreach ($items as $item)
        {
            if ($item->getProductType() != Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
                $productIds[] = $riseOrderModel->isRiseLmsProduct($item);
            }
        }

        //Callback to remove empty indexes from the array
        $filteredProductIds = array_filter($productIds, function($var){return !is_null($var);} );

        if ($filteredProductIds)
        {

            //Add required data to request body
            $customerEmail = $order->getCustomerEmail();

            $riseOrderModel->addCourseRestrictionData('email' , $customerEmail);
            $riseOrderModel->addCourseRestrictionData('product_ids', $filteredProductIds);

            /**
             * put email and product_ids[]
             */
            $result = $riseOrderModel->removeAccessToCoursePut();

            if (!$result)
            {
                Mage::log('Order put failed at observer.php, check submission queue table' . $result);
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
