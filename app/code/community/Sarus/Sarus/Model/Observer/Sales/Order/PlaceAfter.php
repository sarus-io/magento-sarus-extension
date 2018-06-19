<?php

use Sarus\Request\User as SarusUser;
use Sarus\Request\Product\Purchase as SarusPurchase;

class Sarus_Sarus_Model_Observer_Sales_Order_PlaceAfter
{
    /**
     * @var \Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var \Sarus_Sarus_Helper_Order
     */
    protected $_orderHelper;

    /**
     * @var \Sarus_Sarus_Model_Queue
     */
    protected $_queue;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        $this->_orderHelper = Mage::helper('sarus_sarus/order');
        $this->_queue = Mage::getModel('sarus_sarus/queue');
    }

    /**
     * @param \Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var \Mage_Sales_Model_Order $order */
        $order = $observer->getData('order');

        if (!$this->_configGeneral->isEnabled($order->getStoreId())) {
            return;
        }

        $sarusProductUuids = $this->_orderHelper->getSarusProductUuids($order);
        if (empty($sarusProductUuids)){
            return;
        }

        $sarusRequest = new SarusPurchase($sarusProductUuids, $this->_createSarusUser($order));

        $this->_queue->addRequest($sarusRequest, $order->getStoreId());
    }

    /**
     * @param \Mage_Sales_Model_Order $order
     * @return \Sarus\Request\User
     */
    protected function _createSarusUser($order)
    {
        $billingAddress = $order->getBillingAddress();

        $sarusUser = new SarusUser(
            $order->getCustomerEmail(),
            $billingAddress->getFirstname(),
            $billingAddress->getLastname(),
            $order->getCustomerId()
        );
        $sarusUser->setAddress1($billingAddress->getStreet()[0]);
        if (isset($billingAddress->getStreet()[1])) {
            $sarusUser->setAddress2($billingAddress->getStreet()[1]);
        }

        $sarusUser->setCity($billingAddress->getCity());
        $sarusUser->setRegion($billingAddress->getRegion());
        $sarusUser->setPostalCode($billingAddress->getPostcode());
        $sarusUser->setCountry($billingAddress->getCountryId());

        return $sarusUser;
    }
}
