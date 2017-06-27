<?php

class Swarming_RiseLms_Model_Service_OrderComplete
{
    const ENDPOINT = '/v1/purchase';

    /**
     * @var Swarming_RiseLms_Model_Submission_Manager
     */
    protected $_submissionManager;

    public function __construct()
    {
        $this->_submissionManager = Mage::getModel('swarming_riselms/submission_manager');
    }

    /**
     * @param Swarming_RiseLms_Model_Submission $submission
     * @return bool
     */
    public function sendOrderPurchase($submission)
    {
        $submission->setApiMethod(Swarming_RiseLms_Model_Http::METHOD_POST);
        $submission->setApiEndpoint(self::ENDPOINT);

        try {
            $this->_submissionManager->sendSubmission($submission);
            $result = true;
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $this->_submissionManager->addSubmissionToQueue($submission, $e->getMessage());
            $result = false;
        }

        return $result;
    }
}
