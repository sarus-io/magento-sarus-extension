<?php

/**
 * Created by PhpStorm.
 * User: matt
 * Date: 2/9/16
 * Time: 3:01 PM
 */
class Swarming_RiseLms_Model_ResyncSubmission extends Swarming_RiseLms_Model_Abstract_Connector
{
    /** Override @var $apiPath if you need to hit another API */
    protected $_rewriteApiPath = '';

    private $_endpointConfigPath = '';

    /**
     * Called before the connector is initialized
     */
    protected function _beforeInitConnector()
    {
        $this->_endpointPath = Mage::getStoreConfig($this->_endpointConfigPath);
        parent::_beforeInitConnector();
    }

    public function setApiEndpoint($api)
    {
        $this->_endpointPath = $api;
    }

    /**
     * Add the stored JSON array data to the data var for post
     * @param $data
     */
    public function addResyncData($data)
    {
        foreach ($data as $key => $value)
        {
            $this->_data[$key] = $value;
        }
    }

    /**
     * Call resyncPost when explicitly dealing with resync
     * @return array|Exception
     */
    public function resyncPost($collection)
    {
        /**
         * Loop through each submission
         * Set the API endpoint to resync to
         */
        foreach ($collection as $resyncOrder)
        {
            $this->_endpointPath = $resyncOrder->getApiMethod();
            $this->addResyncData(json_decode($resyncOrder->getJson()));

            $result = $this->post();

            if ($result['isSuccess'])
            {
                //PHP 7 Correction Please Check this.. Unsure of end result 
                $value = array_key_exists('message', $result) ? $result['message'] : '';
                
                $this->updateResyncOrder($resyncOrder, $value);
            } else {
                Mage::log('Order post failed, check submission queue table' . $result);
            }
        }
    }

    /**
     * Update failed resubmissions by passing the Submission Queue model and Submission Object
     * @param $resyncOrder
     * @param $result
     */
    protected function updateResyncOrder($resyncOrder, $result)
    {
        $resyncCount = $resyncOrder->getCounter();
        if ($resyncOrder->getCounter() < 9)
        {
            $resyncCount += 1;

            $resyncOrder->setMessages($result['message']);
            $resyncOrder->setCounter($resyncCount);
            ($result['isSuccess']) ? $resyncOrder->setSuccess(1) : $resyncOrder->setSuccess(0);

            $resyncOrder->save();
        } else {
            $notification = Mage::getModel('swarming_riselms/adminNotification');
            $notification->sendNotification(
                'Maximum resubmission limit reached',
                array('The Mmaximum number of resubmissions has been reached for this order, please refer to the queue.' => 'Attempt #' . $resyncCount)
            );
        }
    }
}