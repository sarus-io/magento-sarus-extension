<?php

class Swarming_RiseLms_Model_Cron_SubmissionQueue
{
    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @var Swarming_RiseLms_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_RiseLms_Model_Submission_Manager
     */
    protected $_submissionManager;

    public function __construct()
    {
        $this->_app = Mage::app();
        $this->_configGeneral = Mage::getModel('swarming_riselms/config_general');
        $this->_submissionManager = Mage::getModel('swarming_riselms/submission_manager');
    }

    /**
     * @return Swarming_RiseLms_Model_Resource_Submissionqueue_Collection
     */
    public function _createSubmissionQueueCallection()
    {
        return Mage::getResourceModel('swarming_riselms/submissionqueue_collection');
    }

    /**
     * @return void
     */
    public function execute()
    {
        $stores = $this->_app->getStores();

        foreach (array_keys($stores) as  $storeId) {
            if (!$this->_configGeneral->isEnabled($storeId)) {
                continue;
            }

            $this->_submissionManager->resendFailed($storeId);
        }
    }
}
