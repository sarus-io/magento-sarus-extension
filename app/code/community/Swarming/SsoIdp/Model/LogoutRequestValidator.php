<?php

class Swarming_SsoIdp_Model_LogoutRequestValidator
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Swarming_SsoIdp_Model_Config_Sp
     */
    protected $_configSp;

    /**
     * @var Swarming_SsoIdp_Helper_SpCredentials
     */
    protected $_spCredentials;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
        $this->_configSp = Mage::getModel('swarming_ssoidp/config_sp');
        $this->_spCredentials = Mage::helper('swarming_ssoidp/spCredentials');
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @param Mage_Customer_Model_Customer|null $customer
     * @return bool
     */
    public function validate($logoutRequest, $customer = null)
    {
        $this->_validateIssuer($logoutRequest);
        $this->_validateIfLogoutConfigured();

        $this->_validateNotOnOrAfter($logoutRequest);

        $this->_validateSignature($logoutRequest);

        $this->_validateDestination($logoutRequest);
        $this->_validateNameIdFormat($logoutRequest);
        $this->_validateNameId($logoutRequest, $customer);

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateIssuer($logoutRequest)
    {
        if (null == $logoutRequest->getIssuer()) {
            Mage::throwException('Issuer is not specified.');
        }

        if ($logoutRequest->getIssuer()->getValue() !== $this->_configSp->getEntityId()) {
            Mage::throwException("SP '{$logoutRequest->getIssuer()->getValue()}' unknown issuer.");
        }
        return true;
    }

    protected function _validateIfLogoutConfigured()
    {
        if (!$this->_configSp->getSingleLogoutUrl()) {
            Mage::throwException('The SP is not configured for logout.');
        }
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateNotOnOrAfter($logoutRequest)
    {
        if (!$logoutRequest->getNotOnOrAfterTimestamp()) {
            return true;
        }

        if ($logoutRequest->getNotOnOrAfterTimestamp() + $this->_configIdp->getAllowedSecondsSkew() <= time()) {
            $nowString = \LightSaml\Helper::time2string(time());
            Mage::throwException("NotOnOrAfter: {$logoutRequest->getNotOnOrAfterString()}, now {$nowString}.");
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateSignature($logoutRequest)
    {
        /** @var \LightSaml\Model\XmlDSig\SignatureXmlReader $signatureReader */
        $signatureReader = $logoutRequest->getSignature();
        if ($this->_configIdp->isWantLogoutRequestSigned() && !$signatureReader) {
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
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateDestination($logoutRequest)
    {
        if (!$logoutRequest->getDestination()) {
            Mage::throwException('Destination url is not specified.');
        }

        if ($this->_configIdp->getLogoutUrl() != $logoutRequest->getDestination()) {
            Mage::throwException("Destination url is {$logoutRequest->getDestination()}, expected {$this->_configIdp->getLogoutUrl()}.");
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateNameIdFormat($logoutRequest)
    {
        if (!$logoutRequest->getNameID() || !$logoutRequest->getNameID()->getFormat()) {
            Mage::throwException('Name ID format is not specified.');
        }

        if ($logoutRequest->getNameID()->getFormat() !== $this->_configSp->getNameIdFormat()) {
            Mage::throwException("Name ID format is '{$logoutRequest->getNameID()->getFormat()}', expected {$this->_configSp->getNameIdFormat()}.");
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutRequest $logoutRequest
     * @param Mage_Customer_Model_Customer|null $customer
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateNameId($logoutRequest, $customer = null)
    {
        if (!$logoutRequest->getNameID() || !$logoutRequest->getNameID()->getValue()) {
            Mage::throwException('Name ID is not specified.');
        }

        if ($customer && $customer->getDataUsingMethod($this->_configSp->getNameId()) != $logoutRequest->getNameID()->getValue()) {
            Mage::throwException('Wrong Name ID value.');
        }

        return true;
    }
}
