<?php

class Sarus_Sarus_Model_Submission_Manager
{
    /**
     * @var Sarus_Sarus_Model_Http
     */
    protected $_http;

    /**
     * @var Sarus_Sarus_Model_Config_Api
     */
    protected $_configApi;

    public function __construct()
    {
        $this->_http = Mage::getModel('sarus_sarus/http');
        $this->_configApi = Mage::getModel('sarus_sarus/config_api');
    }

    /**
     * @return Sarus_Sarus_Model_Resource_Submission_Collection
     */
    protected function _createSubmissionCollection()
    {
        return Mage::getResourceModel('sarus_sarus/submission_collection');
    }

    /**
     * @param Sarus_Sarus_Model_Submission $submission
     * @return array|null
     * @throws Mage_Core_Exception
     */
    public function sendSubmission($submission)
    {
        $this->_http->initHttpClient($submission->getStoreId());
        return $this->_http->doRequest(
            $submission->getApiMethod(),
            $submission->getApiEndpoint(),
            $submission->exportData(),
            $submission->getStoreId()
        );
    }

    /**
     * @param Sarus_Sarus_Model_Resource_Submission_Collection $submissions
     * @return void
     */
    public function sendSubmissions($submissions)
    {
        /** @var Sarus_Sarus_Model_Submission $submission */
        foreach ($submissions as $submission) {
            try {
                $this->sendSubmission($submission);
                $this->_successSubmission($submission);
            } catch (\Exception $e) {
                $this->_failSubmission($submission, $e->getMessage());
            }
        }
    }

    /**
     * @param int[] $submissionIds
     * @return int
     */
    public function resendByIds(array $submissionIds)
    {
        $submissions = $this->_createSubmissionCollection();
        $submissions->filterFailed();
        if ($submissionIds) {
            $submissions->filterIds($submissionIds);
        }

        $this->sendSubmissions($submissions);

        return $submissions->count();
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    public function resendFailed($storeId = null)
    {
        $submissions = $this->_createSubmissionCollection();
        $submissions->filterFailed($this->_configApi->getMaxTimeResend($storeId));
        if ($storeId) {
            $submissions->filterStore($storeId);
        }

        $this->sendSubmissions($submissions);
    }

    /**
     * @param Sarus_Sarus_Model_Submission $submission
     * @param string $message
     * @return void
     */
    public function addSubmissionToQueue($submission, $message)
    {
        $submission->setSuccess(false);
        $submission->setErrorMessage($message);
        $submission->save();
    }

    /**
     * @param Sarus_Sarus_Model_Submission $submission
     * @return void
     */
    protected function _successSubmission($submission)
    {
        $counter = $submission->getCounter();
        $submission->setCounter(++$counter);
        $submission->setSuccess(true);
        $submission->save();
    }

    /**
     * @param Sarus_Sarus_Model_Submission $submission
     * @param string $message
     * @return void
     */
    protected function _failSubmission($submission, $message)
    {
        $counter = $submission->getCounter();
        $submission->setCounter(++$counter);
        $submission->setErrorMessage($message);
        $submission->save();
    }
}
