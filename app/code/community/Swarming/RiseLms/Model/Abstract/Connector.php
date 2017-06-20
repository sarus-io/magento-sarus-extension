<?php

/**
 * Rise LMS Connector class
 * User: mattsherer
 * Date: 1/19/16
 * Time: 8:30 AM
 */

abstract class Swarming_RiseLms_Model_Abstract_Connector extends Mage_Core_Model_Abstract
{
    /** @var  Zend_Rest_Client */
    protected $_client;

    protected $_authTokenConfigPath = 'rise_api_auth_token';

    protected $_baseUrlConfigPath = 'rise_api_base_url';

    protected $_params = array();

    protected $_data = array();

    protected $_encodingType = '';

    protected $_endpointPath = null;

    protected $_rewriteApiPath = '';

    const MIN_HEADER_LIMIT_ALLOWED = 0;

    const MIN_BODY_LIMIT_ALLOWED = 0;

    /** @var string Config Path */
    protected $_apiPath = '';


    public function _construct()
    {
        $path = (empty($this->_rewriteApiPath) ? Mage::helper('swarming_riselms/data')->getRiseLmsConfig($this->_baseUrlConfigPath) : $this->_rewriteApiPath);

        if ($path)
        {
            $this->_apiPath = $path;
        }
        $this->_beforeInitConnector();
        $this->_initConnector();
        $this->_afterInitConnector();
    }

    /**
     * Called before the connector is initialized
     */
    protected function _beforeInitConnector()
    {
    }

    /**
     * Initialize the connector
     */
    protected function _initConnector()
    {
        /** Apply validation if $this->_apiPath does not exist */
        $this->_client = new Zend_Rest_Client($this->_apiPath);
    }

    /**
     * Called after the connector is initialized
     */
    protected function _afterInitConnector()
    {
        // Add Authorization header with special Bearer token
        $this->addHeaderParam('Authorization', 'Bearer ' . $this->getAuthToken());
    }

    protected function getAuthToken()
    {
        return Mage::helper('swarming_riselms/data')->getRiseLmsConfig($this->_authTokenConfigPath);
    }

    /**
     * Adds a header parameter
     * @param $key
     * @param $value
     */
    protected function addHeaderParam($key, $value)
    {
        $this->_params[$key] = $value;
    }

    /**
     * Adds data to the body of the request
     * @param $key
     * @param $value
     */
    protected function addBodyData($key, $value)
    {
        $this->_data[$key] = $value;
    }

    /**
     * Sends a get request to the URI
     * @return array | Exception
     */
    protected function get()
    {
        try {
            if (count($this->_params) > Swarming_RiseLms_Model_Abstract_Connector::MIN_HEADER_LIMIT_ALLOWED)
            {
                $this->_client->getHttpClient()->setHeaders($this->_params);
            }

            if (!$this->_endpointPath)
            {
                Mage::log('Configuration Error: No endpoint path has been configured. Please visit the RiseLMS system configurations.', null, 'riselms_api_status.log');
                Mage::throwException('Error: API Endpoint not configured.');
            }

            /** restPost accepts endpoint & postData arguments */
            $result = $this->_client->restGet($this->_endpointPath, $this->_data);

            Mage::log(' ' . $this->_data, null, 'riselms_api_status.log', true);
            Mage::log('Status: ' . $result->getStatus() . ' - Message Body: ' . $result->getBody(), null, 'riselms_api_status.log', true);
            Mage::log('Endpoint: ' . $this->_endpointPath, null, 'riselms_api_status.log', true);
            Mage::log('Data: ' . $this->_data, null, 'riselms_api_status.log', true);
            Mage::log(' ' . $this->_data, null, 'riselms_api_status.log', true);

            if ($result->getStatus() == 200)
            {
                // If status successful return response body
                // Let the extending classes deal with how to handle it
                if ($result = json_decode($result->getBody(), true))
                {
                    $result['isSuccess'] = true;
                }else {
                    $result['isSuccess'] = false;
                }

                return $result;

            } else {
                /**
                 * When status is not 200, notify administrator to investigate the response body
                 */
                $notification = Mage::getModel('swarming_riselms/adminNotification');
                $notification->sendNotification(
                    'Connection to RiseLMS could not be established',
                    array(
                        'User Email' => $this->_data['email'],
                        'Endpoint' => $this->_endpointPath,
                        'API Request Failed' => $result->getStatus() . ' has been returned',
                        'Response' => array(
                            'Response Headers: ' => $result->getheaders(),
                            'Response Body: ' => $result->getBody(),
                            'Response Message: ' => $result->getMessage()
                        ),
                    )
                );
                Mage::throwException('API Request Failed: ' . $result->getStatus() . ' returned.');
            }
        } catch (Exception $e) {
            Mage::logException($e);

            return array('isSuccess' => false, 'message' => $e);
        }
    }

    /**
     * Sends a post request to the URI
     * @return array | Exception
     */
    protected function post()
    {
        try {
            if (count($this->_params) > Swarming_RiseLms_Model_Abstract_Connector::MIN_HEADER_LIMIT_ALLOWED)
            {
                $this->_client->getHttpClient()->setHeaders($this->_params);
            }

            if (!$this->_endpointPath)
            {
                Mage::log('Configuration Error: No endpoint path has been configured. Please visit the RiseLMS system configurations.', null, 'riselms_api_status.log');
                Mage::throwException('Error: API Endpoint not configured.');
            }

            /** restPost accepts endpoint & postData arguments */
            $result = $this->_client->restPost($this->_endpointPath, $this->_data);

            Mage::log('Status: ' . $result->getStatus() . ' - Message Body: ' . $result->getBody(), null, 'riselms_api_status.log', true);
            Mage::log('Endpoint: ' . $this->_endpointPath, null, 'riselms_api_status.log', true);
            //Mage::log('Data: ' . $this->_data, null, 'riselms_api_status.log', true);

            switch ($result->getStatus())
            {
                case 200:
                    // If status successful return response body
                    // Let the extending classes deal with how to handle it
                    if ($result = json_decode($result->getBody(), true))
                    {
                        $result['isSuccess'] = true;
                    }else {
                        $result['isSuccess'] = false;
                    }

                    return $result;
                    break;
                case 201:
                    if ($result = json_decode($result->getBody(), true))
                    {
                        $result['isSuccess'] = true;
                    } else {
                        $result['isSuccess'] = false;
                    }

                    return $result;
                    break;
                case 204:
                    if (is_null($result = json_decode($result->getBody())))
                    {
                        $result['isSuccess'] = true;
                    }

                    return $result;
                    break;
                default:
                    /**
                     * When status is not 200, notify administrator to investigate the response body
                     */
                    $notification = Mage::getModel('swarming_riselms/adminNotification');
                    $notification->sendNotification(
                        'Connection to RiseLMS could not be established',
                        array(
                            'User Email' => $this->_data['email'],
                            'Endpoint' => $this->_endpointPath,
                            'API Request Failed' => $result->getStatus() . ' has been returned',
                            'Response' => array(
                                'Response Headers: ' => $result->getheaders(),
                                'Response Body: ' => $result->getBody(),
                                'Response Message: ' => $result->getMessage()
                            ),
                        )
                    );
                    Mage::throwException('API Request Failed: ' . $result->getStatus() . ' returned.');
            }
        } catch (Exception $e) {
            Mage::logException($e);

            return array('isSuccess' => false, 'message' => $e);
        }
    }


    protected function put()
    {
        try {
            if (count($this->_params) > Swarming_RiseLms_Model_Abstract_Connector::MIN_HEADER_LIMIT_ALLOWED)
            {
                $this->_client->getHttpClient()->setHeaders($this->_params);
            }

            if (!$this->_endpointPath)
            {
                Mage::log('Configuration Error: No endpoint path has been configured. Please visit the RiseLMS system configurations.', null, 'riselms_api_status.log');
                Mage::throwException('Error: API Endpoint not configured.');
            }

            /** restPost accepts endpoint & postData arguments */
            $result = $this->_client->restPut($this->_endpointPath, $this->_data);

            Mage::log(' ' . print_r($this->_data,true), null, 'riselms_api_status.log', true);
            Mage::log('Status: ' . $result->getStatus() . ' - Message Body: ' . $result->getBody(), null, 'riselms_api_status.log', true);
            Mage::log('Endpoint: ' . $this->_endpointPath, null, 'riselms_api_status.log', true);
            Mage::log('Data: ' . $this->_data, null, 'riselms_api_status.log', true);
            Mage::log(' ' . $this->_data, null, 'riselms_api_status.log', true);

            if ($result->getStatus() == 200)
            {
                // If status successful return response body
                // Let the extending classes deal with how to handle it
                if ($result = json_decode($result->getBody(), true))
                {
                    $result['isSuccess'] = true;
                }else {
                    $result['isSuccess'] = false;
                }

                return $result;

            } else {
                /**
                 * When status is not 200, notify administrator to investigate the response body
                 */
                $notification = Mage::getModel('swarming_riselms/adminNotification');
                $notification->sendNotification(
                    'Connection to RiseLMS could not be established',
                    array(
                        'User Email' => $this->_data['email'],
                        'Endpoint' => $this->_endpointPath,
                        'API Request Put Failed' => $result->getStatus() . ' has been returned',
                        'Response' => array(
                            'Response Headers: ' => $result->getheaders(),
                            'Response Body: ' => $result->getBody(),
                            'Response Message: ' => $result->getMessage()
                        ),
                    )
                );
                Mage::throwException('API Put Request Failed: ' . $result->getStatus() . ' returned.');
            }
        } catch (Exception $e) {
            Mage::logException($e);

            return array('isSuccess' => false, 'message' => $e);
        }
    }







}