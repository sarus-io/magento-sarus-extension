<?php

use Sarus\Client\Exception\HttpException as SarusHttpException;

class Sarus_Sarus_Model_Queue
{
    /**
     * @var \Sarus_Sarus_Model_Config_Api
     */
    protected $_configApi;

    /**
     * @var \Sarus_Sarus_Model_Platform
     */
    protected $_platform;

    /**
     * @var \Sarus_Sarus_Model_FailNotification
     */
    protected $_failNotification;

    public function __construct()
    {
        $this->_configApi = Mage::getModel('sarus_sarus/config_api');
        $this->_platform = Mage::getModel('sarus_sarus/platform');
        $this->_failNotification = Mage::getModel('sarus_sarus/failNotification');
    }

    /**
     * @return \Sarus_Sarus_Model_Submission
     */
    protected function _createSubmission()
    {
        return Mage::getModel('sarus_sarus/submission');
    }

    /**
     * @return \Sarus_Sarus_Model_Resource_Submission_Collection
     */
    protected function _createSubmissionCollection()
    {
        return Mage::getResourceModel('sarus_sarus/submission_collection');
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @param int $storeId
     * @return void
     */
    public function addRequest(\Sarus\Request $sarusRequest, $storeId)
    {
        $submissionRecord = $this->_createSubmission();

        $submissionRecord->importRequest($sarusRequest);
        $submissionRecord->setStoreId($storeId);
        $submissionRecord->setCounter(0);
        $submissionRecord->setStatus(Sarus_Sarus_Model_Submission::STATUS_PENDING);

        $submissionRecord->save();
    }

    /**
     * @param \Sarus_Sarus_Model_Resource_Submission_Collection $submissionCollection
     * @return int
     */
    public function sendSubmissions($submissionCollection)
    {
        $counter = 0;
        /** @var \Sarus_Sarus_Model_Submission $submissionRecord */
        foreach ($submissionCollection as $submissionRecord) {
            $counter += $this->_processSubmissionRecord($submissionRecord) ? 1 : 0;
        }

        return $counter;
    }

    /**
     * @param \Sarus_Sarus_Model_Submission $submissionRecord
     * @return bool
     */
    protected function _processSubmissionRecord($submissionRecord)
    {
        $storeId = $submissionRecord->getStoreId();

        $sarusRequest = $submissionRecord->exportRequest();

        try {
            $this->_platform->sendRequest($sarusRequest, $storeId);
            $submissionRecord->setStatus(Sarus_Sarus_Model_Submission::STATUS_DONE);
            $result = true;
        } catch (SarusHttpException $e) {
            $submissionRecord->setErrorMessage($e->getMessage());
            $submissionRecord->setStatus(Sarus_Sarus_Model_Submission::STATUS_FAIL);

            if (($submissionRecord->getCounter() + 1) === $this->_configApi->getMaxTimeResend($storeId)) {
                $this->_failNotification->notify(
                    $storeId,
                    $this->_fetchCustomerEmail($sarusRequest),
                    $e->getRequest(),
                    $e->getResponse()
                );
            }
            $result = false;
        }

        $counter = $submissionRecord->getCounter() + 1;
        $submissionRecord->setCounter($counter);
        $submissionRecord->save();

        return $result;
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @return string|null
     */
    protected function _fetchCustomerEmail($sarusRequest)
    {
        $requestBody = $sarusRequest->getBody();
        $customerEmail = !empty($requestBody['user']['email'])
            ? $requestBody['user']['email']
            : null;
        $customerEmail = $customerEmail === null && !empty($requestBody['email'])
            ? $requestBody['email']
            : $customerEmail;

        return $customerEmail;
    }

    /**
     * @param int[] $submissionIds
     * @return void
     */
    public function deleteByIds(array $submissionIds)
    {
        $submissionCollection = $this->_createSubmissionCollection();
        if ($submissionIds) {
            $submissionCollection->filterIds($submissionIds);
        }

        /** @var \Sarus_Sarus_Model_Submission $submission */
        foreach ($submissionCollection as $submissionRecord) {
            $submissionRecord->delete();
        };
    }
}
