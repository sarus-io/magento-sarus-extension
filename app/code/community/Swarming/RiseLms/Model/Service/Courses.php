<?php

class Swarming_RiseLms_Model_Service_Courses
{
    const ENDPOINT = '/v1/participations';

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
     * @return array
     */
    public function getCourses($email, $storeId = null)
    {
        $submission = $this->_createSubmission();
        $submission->setStoreId($storeId);
        $submission->setApiEndpoint(self::ENDPOINT);
        $submission->setApiMethod(Swarming_RiseLms_Model_Http::METHOD_GET);
        $submission->importData(array('email' => $email));

        try {
            $result = $this->_submissionManager->sendSubmission($submission);
            $courses = $this->_readCourses($result);
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            $courses = array();
        }
        return $courses;
    }

    /**
     * @param array|mixed $result
     * @return mixed
     */
    protected function _readCourses($result)
    {
        if (empty($result['data'])) {
            Mage::throwException('RiseLms courses are not sent.');
        }
        return (array)$result['data'];
    }
}
