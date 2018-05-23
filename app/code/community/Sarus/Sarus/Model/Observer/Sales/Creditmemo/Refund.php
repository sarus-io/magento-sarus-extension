<?php

class Swarming_Sarus_Model_Observer_Sales_Creditmemo_Refund
{
    /**
     * @var Swarming_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_Sarus_Helper_Creditmemo
     */
    protected $_creditmemoHelper;

    /**
     * @var Swarming_Sarus_Model_Service_Creditmemo
     */
    protected $_creditmemoService;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_sarus/config_general');
        $this->_creditmemoHelper = Mage::helper('swarming_sarus/creditmemo');
        $this->_creditmemoService = Mage::getModel('swarming_sarus/service_creditmemo');
    }

    /**
     * @return Swarming_Sarus_Model_Submission
     */
    public function _createSubmission()
    {
        return Mage::getModel('swarming_sarus/submission');
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

        $sarusProductUuids = $this->_creditmemoHelper->getSarusProductUuids($creditmemo);
        if (empty($sarusProductUuids)){
            return;
        }

        $data = array(
            'email' => $creditmemo->getOrder()->getCustomerEmail(),
            'product_ids' => $this->_creditmemoHelper->getSarusProductIds($creditmemo), // TODO Remove after BrainMD migration
            'product_uuids' => $sarusProductUuids
        );

        $submission = $this->_createSubmission();
        $submission->setStoreId($creditmemo->getStoreId());
        $submission->importData($data);

        $this->_creditmemoService->removeAccessToCourse($submission);
    }
}
