<?php

class Sarus_Sarus_Model_Config_Source_ApiAction
{
    /**
     * @var Sarus_Sarus_Helper_Data
     */
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('sarus_sarus');
    }

    /**
     * @return array
     */
    public function toOptionHash()
    {
        return array(
            Sarus_Sarus_Model_Service_Courses::ENDPOINT => $this->_helper->__('Get Courses'),
            Sarus_Sarus_Model_Service_Creditmemo::ENDPOINT => $this->_helper->__('Deactivate Course'),
            Sarus_Sarus_Model_Service_OrderComplete::ENDPOINT => $this->_helper->__('Purchase Course'),
            Sarus_Sarus_Model_Service_UnlinkProduct::ENDPOINT => $this->_helper->__('Unlink Product'),
        );
    }
}
