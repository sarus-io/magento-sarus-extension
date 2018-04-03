<?php

class Swarming_Sarus_Model_Cron_SubmissionQueue
{
    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @var Swarming_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_Sarus_Model_Submission_Manager
     */
    protected $_submissionManager;

    public function __construct()
    {
        $this->_app = Mage::app();
        $this->_configGeneral = Mage::getModel('swarming_sarus/config_general');
        $this->_submissionManager = Mage::getModel('swarming_sarus/submission_manager');
    }

    /**
     * @return Swarming_Sarus_Model_Resource_Submissionqueue_Collection
     */
    public function _createSubmissionQueueCallection()
    {
        return Mage::getResourceModel('swarming_sarus/submissionqueue_collection');
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
