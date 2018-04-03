<?php

class Swarming_RiseLms_Model_Http
{
    const METHOD_GET = 'get';
    const METHOD_POST = 'post';
    const METHOD_PUT = 'put';

    /**
     * @var Swarming_RiseLms_Model_Config_Api
     */
    protected $_configApi;

    /**
     * @var Zend_Rest_Client
     */
    protected $_client;

    /**
     * @var Swarming_RiseLms_Model_Http_Logger
     */
    protected $_httpLogger;

    /**
     * @var Swarming_RiseLms_Model_Http_FailNotification
     */
    protected $_failNotification;

    /**
     * @var array
     */
    protected $_allowedResponseStatuses = array(
        self::METHOD_GET => [200],
        self::METHOD_POST => [200, 201, 204],
        self::METHOD_PUT => [200]
    );

    public function __construct()
    {
        $this->_configApi = Mage::getModel('swarming_riselms/config_api');
        $this->_httpLogger = Mage::getModel('swarming_riselms/http_logger');
        $this->_failNotification = Mage::getModel('swarming_riselms/http_failNotification');
    }

    /**
     * @param int|string|null $storeId
     * @return void
     */
    public function initHttpClient($storeId = null)
    {
        $this->_client = new Zend_Rest_Client($this->_configApi->getBaseUrl($storeId));
        $this->_client->getHttpClient()->setHeaders('Authorization', 'Bearer ' . $this->_configApi->getAuthToken($storeId));
    }

    /**
     * @return Zend_Rest_Client
     */
    protected function _getClient()
    {
        if (!$this->_client) {
            $this->initHttpClient();
        }
        return $this->_client;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $data
     * @param int|null $storeId
     * @return array|null
     * @throws Mage_Core_Exception
     */
    public function doRequest($method, $path, array $data = [], $storeId = null)
    {
        /** @var Zend_Http_Response $response */
        $response = $this->_getClient()->{'rest' . $method}($path, $data);

        if ($this->_configApi->isDebug($storeId)) {
            $this->_httpLogger->logRequest($this->_getClient()->getUri(), $method, $data, $response);
        }

        if (!in_array($response->getStatus(), $this->_allowedResponseStatuses[$method])) {
            $this->_failNotification->notify($path, $data, $response, $storeId);
            Mage::throwException($this->_buildErrorMessage($response));
        }

        $result = $this->_decodeResponseBody($response);
        if (null === $result && $response->getStatus() != 204) {
            Mage::throwException('API Empty response returned.');
        }

        return $result;
    }

    /**
     * @param Zend_Http_Response $response
     * @return string
     */
    protected function _buildErrorMessage($response)
    {
        $message = 'API Request Failed: ' . $response->getStatus() . ' returned.';

        $result = $this->_decodeResponseBody($response);
        if (!empty($result['errorMessage'])) {
            $message .= ' ErrorMessage: ' . $result['errorMessage'];
        }
        return $message;
    }


    /**
     * @param Zend_Http_Response $response
     * @return array|null
     */
    protected function _decodeResponseBody($response)
    {
        return json_decode($response->getBody(), true);
    }
}
