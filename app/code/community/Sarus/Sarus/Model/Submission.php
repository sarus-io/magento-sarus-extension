<?php

/**
 * @method int getStoreId()
 * @method $this setStoreId(int $storeId)
 * @method string getApiMethod()
 * @method $this setApiMethod(string $apiMethod)
 * @method string getApiEndpoint()
 * @method $this setApiEndpoint(string $apiEndpoint)
 * @method string getJson()
 * @method $this setJson(string $json)
 * @method int getCounter()
 * @method $this setCounter(int $counter)
 * @method int getSubmissionTime()
 * @method $this setSubmissionTime(string $submissionTime)
 * @method bool getSuccess()
 * @method $this setSuccess(bool $success)
 * @method string getErrorMessage()
 * @method $this setErrorMessage(string $errorMessage)
 */
class Swarming_Sarus_Model_Submission extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('swarming_sarus/submission');
    }

    /**
     * @param array $data
     * @return $this
     */
    public function importData(array $data)
    {
        $this->setJson(json_encode($data));
        return $this;
    }

    /**
     * @return array
     */
    public function exportData()
    {
        return (array)json_decode($this->getJson(), 1);
    }
}
