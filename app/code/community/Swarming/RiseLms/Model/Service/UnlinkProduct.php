<?php

class Swarming_RiseLms_Model_Service_UnlinkProduct
{
    const ENDPOINT = '/v1/ecommerce/{product}/unlink';

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
     * @param int $productId
     * @param int|string $storeId
     * @return bool
     */
    public function unlinkProduct($productId, $storeId)
    {
        $submission = $this->_createSubmission();
        $submission->setStoreId($storeId);
        $submission->setApiEndpoint(str_replace('{product}', $productId, self::ENDPOINT));
        $submission->setApiMethod(Swarming_RiseLms_Model_Http::METHOD_POST);

        try {
            $this->_submissionManager->sendSubmission($submission);
            $result = true;
        } catch (Exception $e) {
            Mage::logException($e);
            $result = false;
        }

        return $result;
    }
}
