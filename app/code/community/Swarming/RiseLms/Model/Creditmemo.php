<?php

class Swarming_RiseLms_Model_Creditmemo extends Swarming_RiseLms_Model_Abstract_Connector
{
    /** Override @var $apiPath if you need to hit another API */
    protected $_rewriteApiPath = '';
    private $_endpointConfigPath = 'riselms_general/general/rise_api_creditmemo_endpoint';

    /**
     * Called before the connector is initialized
     */
    protected function _beforeInitConnector()
    {
        $this->_endpointPath = Mage::getStoreConfig($this->_endpointConfigPath);
        parent::_beforeInitConnector();
    }

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return mixed
     */
    public function isRiseLmsProduct($item)
    {
        $riseCourseUuid = $item->getRiseCourseUuid();

        if (!empty($riseCourseUuid))
        {
            return $item->getProductId();
        } else {
            return Mage::getModel('swarming_riselms/courses')->getCourseUuid($item);
        }
    }


    public function addCourseRestrictionData($label, $data)
    {
        //Mage::log($label . ',' . print_r($data , true));
        $this->addBodyData($label, $data);
    }

    /**
     * Call creditMemoProcessedPut when explicitly dealing with orders
     * @return array|Exception
     */
    public function removeAccessToCoursePut()
    {
        $result = $this->put();

        if (isset($result['isSuccess']) && !$result['isSuccess'])
        {
            $this->createCreditMemoResync($result['message']);
            return false;
        }

        return true;
    }

    /**
     * Create failed submissions by passing the response message
     * @param $message
     */
    protected function createCreditMemoResync($message)
    {
        /**
         * Create submission entry when you don't receive Status 200
         */
        $submissionQueue = Mage::getModel('swarming_riselms/submissionqueue');
        $timestamp       = Mage::helper('swarming_riselms/data')->getTimeStamp();
        $submissionQueue->setSuccess(0);
        $submissionQueue->setJson(json_encode($this->_data));
        $submissionQueue->setSubmissionTime($timestamp);
        $submissionQueue->setErrorMessage($message->getMessage());
        $submissionQueue->setApiMethod($this->_endpointPath);
        $submissionQueue->save();
    }
}