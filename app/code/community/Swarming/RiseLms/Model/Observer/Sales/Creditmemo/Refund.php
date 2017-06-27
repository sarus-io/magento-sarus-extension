<?php

class Swarming_RiseLms_Model_Observer_Sales_Creditmemo_Refund
{
    /**
     * @var Swarming_RiseLms_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_RiseLms_Helper_Creditmemo
     */
    protected $_creditmemoHelper;

    /**
     * @var Swarming_RiseLms_Model_Service_Creditmemo
     */
    protected $_creditmemoService;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_riselms/config_general');
        $this->_creditmemoHelper = Mage::helper('swarming_riselms/creditmemo');
        $this->_creditmemoService = Mage::getModel('swarming_riselms/service_creditmemo');
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
        /** @var Mage_Sales_Model_Order_Creditmemo $creditmemo */
        $creditmemo = $observer->getData('creditmemo');

        if (!$this->_configGeneral->isEnabled($creditmemo->getStoreId())) {
            return;
        }

        $riseLmsProductUuids = $this->_creditmemoHelper->getRiseLmsProductUuids($creditmemo);
        if (empty($riseLmsProductUuids)){
            return;
        }

        $data = array(
            'email' => $creditmemo->getOrder()->getCustomerEmail(),
            'product_ids' => $this->_creditmemoHelper->getRiseLmsProductIds($creditmemo), // TODO
            'product_uuids' => $riseLmsProductUuids
        );

        $submission = $this->_createSubmission();
        $submission->setStoreId($creditmemo->getStoreId());
        $submission->importData($data);

        $this->_creditmemoService->removeAccessToCourse($submission);
    }
}
