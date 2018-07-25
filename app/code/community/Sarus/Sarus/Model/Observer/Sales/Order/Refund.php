<?php

use Sarus\Request\Enrollment\Deactivate as SarusDeactivate;

class Sarus_Sarus_Model_Observer_Sales_Order_Refund
{
    /**
     * @var \Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var \Sarus_Sarus_Helper_Creditmemo
     */
    protected $_creditmemoHelper;

    /**
     * @var \Sarus_Sarus_Model_Queue
     */
    protected $_queue;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        $this->_creditmemoHelper = Mage::helper('sarus_sarus/creditmemo');
        $this->_queue = Mage::getModel('sarus_sarus/queue');
    }
    /**
     * @param \Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $observer->getData('creditmemo');

        if (!$this->_configGeneral->isEnabled($creditmemo->getStoreId())) {
            return;
        }

        $sarusProductUuids = $this->_creditmemoHelper->getSarusProductUuids($creditmemo);
        if (empty($sarusProductUuids)){
            return;
        }

        $sarusRequest = new SarusDeactivate($creditmemo->getOrder()->getCustomerEmail(), $sarusProductUuids);
        $this->_queue->sendRequest($sarusRequest, $creditmemo->getStoreId());
    }
}
