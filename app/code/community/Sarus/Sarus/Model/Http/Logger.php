<?php

class Swarming_Sarus_Model_Http_Logger
{
    /**
     * @var string
     */
    protected $_fileName = 'sarus_api_status.log';

    /**
     * @param string $path
     * @param string $method
     * @param array $data
     * @param Zend_Http_Response $response
     * @return void
     */
    public function logRequest($path, $method, $data, $response)
    {
        $this->_logEntity('');
        $this->_logEntity('Endpoint: ' . $method . ' - ' . $path);
        $this->_logEntity('Data: ' . json_encode($data));
        $this->_logEntity('Status: ' . $response->getStatus() . ' - Message Body: ' . $response->getBody());
    }

    /**
     * @param $data
     * @return void
     */
    protected function _logEntity($data)
    {
        Mage::log($data, Zend_Log::DEBUG, $this->_fileName, true);
    }
}
