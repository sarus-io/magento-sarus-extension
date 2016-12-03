<?php

/**
 * Connector class for order complete
 * User: mattsherer
 * Date: 1/19/16
 * Time: 10:07 AM
 */

class Swarming_RiseLms_Model_UnlinkProduct extends Swarming_RiseLms_Model_Abstract_Connector
{
    /** Override @var $apiPath if you need to hit another API */
    protected $_rewriteApiPath = '';

    private $_endpointConfigPath = 'riselms_general/general/rise_api_unlink_endpoint';

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
    public function addUnlinkHeaderParam($label, $data)
    {
        $this->addHeaderParam($label, $data);
    }

    /**
     * Add data params to order related data
     * @param $label
     * @param $data
     */
    public function addUnlinkData($label, $data)
    {
        $this->addBodyData($label, $data);
    }

    /**
     * Call orderCompletePost when explicitly dealing with orders
     * @return array|Exception
     */
    public function unlinkProductPost($productId)
    {
        $this->_endpointPath .= "/{$productId}/unlink";

        $result = $this->post();

        if (isset($result['isSuccess']) && $result['isSuccess'])
        {
            Mage::getSingleton('core/session')->addSuccess('Product has been successfully unlinked from RiseLMS.');
        } else {
            Mage::getSingleton('core/session')->addError('Product could not be successfully unlinked from RiseLMS.');
        }
    }
}