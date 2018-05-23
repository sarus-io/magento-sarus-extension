<?php

class Sarus_Sarus_Model_Service_Courses
{
    const ENDPOINT = '/v1/participations';

    /**
     * @var Sarus_Sarus_Model_Submission_Manager
     */
    protected $_submissionManager;

    public function __construct()
    {
        $this->_submissionManager = Mage::getModel('sarus_sarus/submission_manager');
    }

    /**
     * @return Sarus_Sarus_Model_Submission
     */
    public function _createSubmission()
    {
        return Mage::getModel('sarus_sarus/submission');
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
        $submission->setApiMethod(Sarus_Sarus_Model_Http::METHOD_GET);
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
            Mage::throwException('Sarus courses are not sent.');
        }
        return (array)$result['data'];
    }
}
