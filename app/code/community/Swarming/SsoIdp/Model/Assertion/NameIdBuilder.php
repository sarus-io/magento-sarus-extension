<?php

class Swarming_SsoIdp_Model_Assertion_NameIdBuilder
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Sp
     */
    protected $_configSp;

    public function __construct()
    {
        $this->_configSp = Mage::getModel('swarming_ssoidp/config_sp');
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return \LightSaml\Model\Assertion\NameID
     */
    protected function _createAssertionNameId()
    {
        return new \LightSaml\Model\Assertion\NameID();
    }

    /**
     * @return \LightSaml\Model\Assertion\NameID
     */
    public function build()
    {
        $nameId = $this->_createAssertionNameId();
        $nameId->setValue($this->_getNameIdValue());
        $nameId->setFormat($this->_configSp->getNameIdFormat());
        return $nameId;
    }

    /**
     * @return string|int
     */
    protected function _getNameIdValue()
    {
        $customer = $this->_getCustomerSession()->getCustomer();
        return $customer->getDataUsingMethod($this->_configSp->getNameId());
    }
}
