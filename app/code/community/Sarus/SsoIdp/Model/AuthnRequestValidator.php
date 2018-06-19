<?php

class Sarus_SsoIdp_Model_AuthnRequestValidator
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
     * @var Sarus_SsoIdp_Helper_SpCredentials
     */
    protected $_spCredentials;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_configSp = Mage::getModel('sarus_ssoidp/config_sp');
        $this->_spCredentials = Mage::helper('sarus_ssoidp/spCredentials');
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validate($authnRequest)
    {
        $this->_validateIssuer($authnRequest);

        $this->_validateSignature($authnRequest);

        $this->_validateDestination($authnRequest);
        $this->_validateNameIdFormat($authnRequest);

        $this->_validateBinding($authnRequest);
        $this->_validateAssertionConsumerServiceUrl($authnRequest);

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateIssuer($authnRequest)
    {
        if (null == $authnRequest->getIssuer()) {
            Mage::throwException('Issuer is not specified.');
        }

        if ($authnRequest->getIssuer()->getValue() !== $this->_configSp->getEntityId()) {
            Mage::throwException("SP '{$authnRequest->getIssuer()->getValue()}' unknown issuer.");
        }
        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateSignature($authnRequest)
    {
        /** @var \LightSaml\Model\XmlDSig\SignatureXmlReader $signatureReader */
        $signatureReader = $authnRequest->getSignature();
        if ($this->_configIdp->isWantAuthnSigned() && !$signatureReader) {
            Mage::throwException('No signature, but is required.');
        }

        if (!$signatureReader) {
            return true;
        }

        $isSignatureValid = $signatureReader->validate($this->_spCredentials->getPublicKey());
        if (!$isSignatureValid) {
            Mage::throwException('Signature is not validated.');
        }
        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateDestination($authnRequest)
    {
        if (!$authnRequest->getDestination()) {
            Mage::throwException('Destination url is not specified.');
        }

        if ($this->_configIdp->getSingleSingOnUrl() != $authnRequest->getDestination()) {
            Mage::throwException("Destination url is {$authnRequest->getDestination()}, expected {$this->_configIdp->getSingleSingOnUrl()}.");
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateNameIdFormat($authnRequest)
    {
        if (!$authnRequest->getNameIDPolicy() || !$authnRequest->getNameIDPolicy()->getFormat()) {
            Mage::throwException('Name ID format is not specified.');
        }

        if ($authnRequest->getNameIDPolicy()->getFormat() !== $this->_configSp->getNameIdFormat()) {
            Mage::throwException("Name ID format is '{$authnRequest->getNameIDPolicy()->getFormat()}', expected {$this->_configSp->getNameIdFormat()}.");
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateBinding($authnRequest)
    {
        if (!$authnRequest->getProtocolBinding()) {
            Mage::throwException('Protocol binding is not specified.');
        }

        if ($this->_configSp->getAssertionConsumerBinding() != $authnRequest->getProtocolBinding()) {
            Mage::throwException("Protocol binding is {$authnRequest->getProtocolBinding()}, expected {$this->_configSp->getAssertionConsumerBinding()}.");
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateAssertionConsumerServiceUrl($authnRequest)
    {
        if (!$authnRequest->getAssertionConsumerServiceURL()) {
            Mage::throwException('Assert consumer service url is not specified.');
        }

        if ($this->_configSp->getAssertionConsumerUrl() != $authnRequest->getAssertionConsumerServiceURL()) {
            Mage::throwException("Assert consumer service url is {$authnRequest->getAssertionConsumerServiceURL()}, expected {$this->_configSp->getAssertionConsumerUrl()}.");
        }

        return true;
    }
}
