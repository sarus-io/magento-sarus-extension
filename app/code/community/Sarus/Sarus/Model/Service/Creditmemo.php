<?php

class Sarus_Sarus_Model_Service_Creditmemo
{
    const ENDPOINT = '/v1/participation/deactivate';

    /**
     * @var Sarus_Sarus_Model_Submission_Manager
     */
    protected $_submissionManager;

    public function __construct()
    {
        $this->_submissionManager = Mage::getModel('sarus_sarus/submission_manager');
    }

    /**
     * @param Sarus_Sarus_Model_Submission $submission
     * @return bool
     */
    public function removeAccessToCourse($submission)
    {
        $submission->setApiMethod(Sarus_Sarus_Model_Http::METHOD_PUT);
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
