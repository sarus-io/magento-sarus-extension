<?php

class Swarming_SsoIdp_Model_LogoutRequestBuilder
{
    /**
     * @var \Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Swarming_SsoIdp_Model_Config_Sp
     */
    protected $_configSp;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_IssuerBuilder
     */
    protected $_issuerBuilder;

    /**
     * @var \Swarming_SsoIdp_Model_Assertion_NameIdBuilder
     */
    protected $_nameIdBuilder;

    /**
     * @var Swarming_SsoIdp_Model_SignatureWriterFactory
     */
    protected $_signatureWriterFactory;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
        $this->_configSp = Mage::getModel('swarming_ssoidp/config_sp');
        $this->_issuerBuilder = Mage::getModel('swarming_ssoidp/assertion_issuerBuilder');
        $this->_nameIdBuilder = Mage::getModel('swarming_ssoidp/assertion_nameIdBuilder');
        $this->_signatureWriterFactory = Mage::getModel('swarming_ssoidp/signatureWriterFactory');
    }

    /**
     * @return Swarming_SsoIdp_Model_Session
     */
    protected function _getSsoSession()
    {
        return Mage::getSingleton('swarming_ssoidp/session');
    }

    /**
     * @return \LightSaml\Model\Protocol\LogoutRequest
     */
    protected function _createLogoutRequest()
    {
        return new \LightSaml\Model\Protocol\LogoutRequest();
    }

    /**
     * @return \LightSaml\Model\Protocol\LogoutRequest
     */
    public function build()
    {
        $logoutResponse = $this-> _createLogoutRequest();
        $logoutResponse->setIssuer($this->_issuerBuilder->build());
        $logoutResponse->setID(\LightSaml\Helper::generateID());
        $logoutResponse->setIssueInstant(new \DateTime());
        $logoutResponse->setNameID($this->_nameIdBuilder->build());
        $logoutResponse->setDestination($this->_configSp->getSingleLogoutUrl());
        $logoutResponse->setSessionIndex($this->_getSsoSession()->getSessionId());

        if ($this->_configIdp->isMessagesSigned()) {
            $logoutResponse->setSignature($this->_signatureWriterFactory->create());
        }
        return $logoutResponse;
    }
}
