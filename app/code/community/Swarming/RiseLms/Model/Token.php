<?php

/**
 * Created by PhpStorm.
 * User: matt
 * Date: 3/10/16
 * Time: 3:49 PM
 */
class Swarming_RiseLms_Model_Token extends Swarming_RiseLms_Model_Abstract_Connector
{
    /** Override @var $apiPath if you need to hit another API */
    protected $_rewriteApiPath = '';

    private $_endpointConfigPath = 'riselms_general/general/rise_api_course_token_endpoint';

    /**
     * Called before the connector is initialized
     */
    protected function _beforeInitConnector()
    {
        $this->_endpointPath = Mage::getStoreConfig($this->_endpointConfigPath);
        parent::_beforeInitConnector();
    }

    /**
     * Add data params to order related data
     * @param $label
     * @param $data
     */
    public function addTokenHeaderParam($label, $data)
    {
        $this->addHeaderParam($label, $data);
    }

    /**
     * Add data params to order related data
     * @param $label
     * @param $data
     */
    public function addTokenData($label, $data)
    {
        $this->addBodyData($label, $data);
    }

    public function tokenPost()
    {
        return $this->post();
    }
}