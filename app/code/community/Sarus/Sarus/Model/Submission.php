<?php

use Sarus\Request\CustomRequest as SarusCustomRequest;

/**
 * @method int getStoreId()
 * @method $this setStoreId($storeId)
 * @method string getRequest()
 * @method $this setRequest($request)
 * @method int getCounter()
 * @method $this setCounter($counter)
 * @method string getStatus()
 * @method $this setStatus($status)
 * @method string getErrorMessage()
 * @method $this setErrorMessage($errorMessage)
 * @method string getCreatingTime()
 * @method $this setCreatingTime($creatingTime)
 * @method string getSubmissionTime()
 * @method $this setSubmissionTime($submissionTime)
 */
class Sarus_Sarus_Model_Submission extends Mage_Core_Model_Abstract
{
    const STATUS_PENDING = 'pending';
    const STATUS_DONE = 'done';
    const STATUS_FAIL = 'fail';

    protected function _construct()
    {
        $this->_init('sarus_sarus/submission');
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @return $this
     */
    public function importRequest(\Sarus\Request $sarusRequest)
    {
        $this->setRequest(json_encode($sarusRequest));
        return $this;
    }

    /**
     * @return \Sarus\Request
     */
    public function exportRequest()
    {
        $sarusRequestData = (array)json_decode($this->getRequest(), 1);
        return SarusCustomRequest::fromArray($sarusRequestData);
    }
}
