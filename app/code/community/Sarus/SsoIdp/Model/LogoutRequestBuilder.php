<?php

class Sarus_SsoIdp_Model_LogoutRequestBuilder
{
    /**
     * @var \Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Sarus_SsoIdp_Model_Config_Sp
     */
    protected $_configSp;

    /**
     * @var Sarus_SsoIdp_Model_Assertion_IssuerBuilder
     */
    protected $_issuerBuilder;

    /**
     * @var \Sarus_SsoIdp_Model_Assertion_NameIdBuilder
     */
    protected $_nameIdBuilder;

    /**
     * @var Sarus_SsoIdp_Model_SignatureWriterFactory
     */
    protected $_signatureWriterFactory;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_configSp = Mage::getModel('sarus_ssoidp/config_sp');
        $this->_issuerBuilder = Mage::getModel('sarus_ssoidp/assertion_issuerBuilder');
        $this->_nameIdBuilder = Mage::getModel('sarus_ssoidp/assertion_nameIdBuilder');
        $this->_signatureWriterFactory = Mage::getModel('sarus_ssoidp/signatureWriterFactory');
    }

    /**
     * @return Sarus_SsoIdp_Model_Session
     */
    protected function _getSsoSession()
    {
        return Mage::getSingleton('sarus_ssoidp/session');
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
