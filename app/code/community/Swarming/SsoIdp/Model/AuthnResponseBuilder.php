<?php

use LightSaml\SamlConstants;

class Swarming_SsoIdp_Model_AuthnResponseBuilder
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_IssuerBuilder
     */
    protected $_issuerBuilder;

    /**
     * @var Swarming_SsoIdp_Model_AssertionBuilder
     */
    protected $_assertionBuilder;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_EncryptorWriterBuilder
     */
    protected $_encryptorWriterBuilder;

    /**
     * @var Swarming_SsoIdp_Model_SignatureWriterFactory
     */
    protected $_signatureWriterFactory;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
        $this->_issuerBuilder = Mage::getModel('swarming_ssoidp/assertion_issuerBuilder');
        $this->_assertionBuilder = Mage::getModel('swarming_ssoidp/assertionBuilder');
        $this->_encryptorWriterBuilder = Mage::getModel('swarming_ssoidp/assertion_encryptorWriterBuilder');
        $this->_signatureWriterFactory = Mage::getModel('swarming_ssoidp/signatureWriterFactory');
    }

    /**
     * @return \LightSaml\Model\Protocol\Response
     */
    protected function _createAuthnResponse()
    {
        return new \LightSaml\Model\Protocol\Response();
    }

    /**
     * @return \LightSaml\Model\Assertion\Issuer
     */
    protected function _createAssertionIssuer()
    {
        return new \LightSaml\Model\Assertion\Issuer();
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
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return \LightSaml\Model\Protocol\Response
     */
    public function build($authnRequest)
    {
        $authnResponse = $this->_createAuthnResponse();
        $authnResponse->setInResponseTo($authnRequest->getId());
        $authnResponse->setID(\LightSaml\Helper::generateID());
        $authnResponse->setIssueInstant(new \DateTime());
        $authnResponse->setDestination($authnRequest->getAssertionConsumerServiceURL());
        $authnResponse->setIssuer($this->_issuerBuilder->build());
        $authnResponse->setStatus($this->_createStatusStatus());

        $assertion = $this->_assertionBuilder->build($authnRequest);
        if ($this->_configIdp->isAssertionEncrypted()) {
            $authnResponse->addEncryptedAssertion($this->_encryptorWriterBuilder->build($assertion));
        } else {
            $authnResponse->addAssertion($assertion);
        }

        if ($this->_configIdp->isMessagesSigned()) {
            $authnResponse->setSignature($this->_signatureWriterFactory->create());
        }

        if ($authnRequest->getRelayState()) {
            $authnResponse->setRelayState($authnRequest->getRelayState());
        }

        return $authnResponse;
    }
}
