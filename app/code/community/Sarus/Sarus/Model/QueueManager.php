<?php

class Sarus_Sarus_Model_QueueManager
{
    /**
     * @var \Sarus_Sarus_Model_Config_Api
     */
    protected $_configApi;

    /**
     * @var \Sarus_Sarus_Model_Queue
     */
    protected $_queue;

    public function __construct()
    {
        $this->_configApi = Mage::getModel('sarus_sarus/config_api');
        $this->_queue = Mage::getModel('sarus_sarus/queue');
    }

    /**
     * @return \Sarus_Sarus_Model_Resource_Submission_Collection
     */
    protected function _createSubmissionCollection()
    {
        return Mage::getResourceModel('sarus_sarus/submission_collection');
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    public function sendPendingSubmissions($storeId = null)
    {
        $submissionCollection = $this->_createSubmissionCollection();
        $submissionCollection->filterStatus(Sarus_Sarus_Model_Submission::STATUS_PENDING);
        $submissionCollection->setOrder('entity_id', Sarus_Sarus_Model_Resource_Submission_Collection::SORT_ORDER_ASC);

        if ($storeId) {
            $submissionCollection->filterStore($storeId);
        }

        $this->_queue->sendSubmissions($submissionCollection);
    }

    /**
     * @param int|null $storeId
     * @return void
     */
    public function sendFailedSubmissions($storeId = null)
    {
        $submissionCollection = $this->_createSubmissionCollection();
        $submissionCollection->filterStatus(Sarus_Sarus_Model_Submission::STATUS_FAIL);
        $submissionCollection->setOrder('entity_id', Sarus_Sarus_Model_Resource_Submission_Collection::SORT_ORDER_ASC);

        if ($storeId) {
            $submissionCollection->filterStore($storeId);
        }

        $threshold = $this->_configApi->getMaxTimeResend();
        if ($threshold > 0) {
            $submissionCollection->filterCounter($threshold);
        }

        $this->_queue->sendSubmissions($submissionCollection);
    }

    /**
     * @param int[] $submissionIds
     * @return int
     * @throws \InvalidArgumentException
     */
    public function sendByIds(array $submissionIds)
    {
        if (empty($submissionIds)) {
            throw new \InvalidArgumentException('$submissionIds cannot be empty.');
        }

        $submissionCollection = $this->_createSubmissionCollection();
        $submissionCollection->filterIds($submissionIds);
        $submissionCollection->setOrder('entity_id', Sarus_Sarus_Model_Resource_Submission_Collection::SORT_ORDER_ASC);

        return $this->_queue->sendSubmissions($submissionCollection);
    }
}
