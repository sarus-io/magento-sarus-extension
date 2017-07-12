<?php

class Swarming_RiseLms_Model_Observer_Sales_Order_PlaceAfter
{
    /**
     * @var Swarming_RiseLms_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_RiseLms_Helper_Order
     */
    protected $_orderHelper;

    /**
     * @var Swarming_RiseLms_Model_Service_OrderComplete
     */
    protected $_orderCompleteService;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_riselms/config_general');
        $this->_orderHelper = Mage::helper('swarming_riselms/order');
        $this->_orderCompleteService = Mage::getModel('swarming_riselms/service_orderComplete');
    }

    /**
     * @return Swarming_RiseLms_Model_Submission
     */
    public function _createSubmission()
    {
        return Mage::getModel('swarming_riselms/submission');
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getData('order');

        if (!$this->_configGeneral->isEnabled($order->getStoreId())) {
            return;
        }

        $riseLmsProductUuids = $this->_orderHelper->getRiseLmsProductUuids($order);
        if (empty($riseLmsProductUuids)){
            return;
        }

        $data = array(
            'user' => $this->_prepareUserData($order),
            'product_ids' => $this->_orderHelper->getRiseLmsProductIds($order), // TODO Remove after BrainMD migration
            'product_uuids' => $riseLmsProductUuids
        );

        $submission = $this->_createSubmission();
        $submission->setStoreId($order->getStoreId());
        $submission->importData($data);

        $this->_orderCompleteService->sendOrderPurchase($submission);
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    protected function _prepareUserData($order)
    {
        $billingAddress = $order->getBillingAddress();
        $data = array(
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
        );
        return $data;
    }
}
