<?php

class Swarming_RiseLms_Model_Config_Source_ApiAction
{
    /**
     * @var Swarming_RiseLms_Helper_Data
     */
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('swarming_riselms');
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return array(
            Swarming_RiseLms_Model_Service_Courses::ENDPOINT => $this->_helper->__('Get Courses'),
            Swarming_RiseLms_Model_Service_Creditmemo::ENDPOINT => $this->_helper->__('Deactivate Course'),
            Swarming_RiseLms_Model_Service_OrderComplete::ENDPOINT => $this->_helper->__('Purchase Course'),
            Swarming_RiseLms_Model_Service_UnlinkProduct::ENDPOINT => $this->_helper->__('Unlink Product'),
        );
    }
}
