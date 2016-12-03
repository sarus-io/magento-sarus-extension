<?php

/**
 * Connector class for order complete
 * User: mattsherer
 * Date: 1/19/16
 * Time: 10:07 AM
 */

class Swarming_RiseLms_Model_Ordercomplete extends Swarming_RiseLms_Model_Abstract_Connector
{
    /** Override @var $apiPath if you need to hit another API */
    protected $_rewriteApiPath = '';

    private $_endpointConfigPath = 'riselms_general/general/rise_api_purchase_endpoint';

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
        }
    }

    /**
     * Add data params to order related data
     * @param $label
     * @param $data
     */
    public function addOrderHeaderParam($label, $data)
    {
        $this->addHeaderParam($label, $data);
    }

    /**
     * Add data params to order related data
     * @param $label
     * @param $data
     */
    public function addOrderData($label, $data)
    {
        $this->addBodyData($label, $data);
    }

    /**
     * Call orderCompletePost when explicitly dealing with orders
     * @return array|Exception
     */
    public function orderCompletePost()
    {
        $result = $this->post();

        if (isset($result['isSuccess']) && !$result['isSuccess'])
        {
            $this->createOrderResync($result['message']);
            return false;
        }

        return true;
    }

    /**
     * Create failed submissions by passing the response message
     * @param $message
     */
    protected function createOrderResync($message)
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