<?php

class Swarming_RiseLms_Model_Service_Token
{
    const ENDPOINT = '/v1/user/login-token';

    /**
     * @var Swarming_RiseLms_Model_Submission_Manager
     */
    protected $_submissionManager;

    public function __construct()
    {
        $this->_submissionManager = Mage::getModel('swarming_riselms/submission_manager');
    }

    /**
     * @return Swarming_RiseLms_Model_Submission
     */
    public function _createSubmission()
    {
        return Mage::getModel('swarming_riselms/submission');
    }

    /**
     * @param string $email
     * @param int|string|null $storeId
     * @return string|null
     */
    public function getToken($email, $storeId = null)
    {
        $submission = $this->_createSubmission();
        $submission->setStoreId($storeId);
        $submission->setApiEndpoint(self::ENDPOINT);
        $submission->setApiMethod(Swarming_RiseLms_Model_Http::METHOD_POST);
        $submission->importData(array('email' => $email));

        try {
            $result = $this->_submissionManager->sendSubmission($submission);
            $token = $this->_readToken($result);
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $token = null;

        }
        return $token;
    }

    /**
     * @param array|mixed $result
     * @return mixed
     */
    protected function _readToken($result)
    {
        if (empty($result['token'])) {
            Mage::throwException('RiseLms token is empty.');
        }
        return $result['token'];
    }
}
