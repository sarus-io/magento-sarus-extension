<?php

use LightSaml\SamlConstants;

class Sarus_SsoIdp_Model_LogoutResponseBuilder
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
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
     * @var Sarus_SsoIdp_Model_SignatureWriterFactory
     */
    protected $_signatureWriterFactory;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_configSp = Mage::getModel('sarus_ssoidp/config_sp');
        $this->_issuerBuilder = Mage::getModel('sarus_ssoidp/assertion_issuerBuilder');
        $this->_signatureWriterFactory = Mage::getModel('sarus_ssoidp/signatureWriterFactory');
    }

    /**
     * @return \LightSaml\Model\Protocol\LogoutResponse
     */
    protected function _createLogoutResponse()
    {
        return new \LightSaml\Model\Protocol\LogoutResponse();
    }

    /**
     * @return \LightSaml\Model\Protocol\Status
     */
    protected function _createStatusStatus()
    {
        return new \LightSaml\Model\Protocol\Status(
            new \LightSaml\Model\Protocol\StatusCode(SamlConstants::STATUS_SUCCESS)
        );
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @return \LightSaml\Model\Protocol\LogoutResponse
     */
    public function build($logoutRequest)
    {
        $logoutResponse = $this->_createLogoutResponse();
        $logoutResponse->setID(\LightSaml\Helper::generateID());
        $logoutResponse->setIssueInstant(new \DateTime());
        $logoutResponse->setIssuer($this->_issuerBuilder->build());
        $logoutResponse->setInResponseTo($logoutRequest->getId());
        $logoutResponse->setDestination($this->_configSp->getSingleLogoutUrl());
        $logoutResponse->setStatus($this->_createStatusStatus());

        if ($this->_configIdp->isMessagesSigned()) {
            $logoutResponse->setSignature($this->_signatureWriterFactory->create());
        }

        if ($logoutRequest->getRelayState()) {
            $logoutResponse->setRelayState($logoutRequest->getRelayState());
        }

        return $logoutResponse;
    }
}
