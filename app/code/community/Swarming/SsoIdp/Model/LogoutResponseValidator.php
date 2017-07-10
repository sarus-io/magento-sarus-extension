<?php

class Swarming_SsoIdp_Model_LogoutResponseValidator
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
     * @param \LightSaml\Model\Protocol\LogoutResponse $logoutResponse
     * @param string|null $requestId
     * @return bool
     * @throws Mage_Core_Exception
     */
    public function validate($logoutResponse, $requestId = null)
    {
        $this->_validateIssuer($logoutResponse);

        $this->_validateSignature($logoutResponse);

        $this->_validateRequestId($logoutResponse, $requestId);
        $this->_validateDestination($logoutResponse);

        $this->_validateStatus($logoutResponse);

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutResponse $logoutResponse
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateIssuer($logoutResponse)
    {
        if (null == $logoutResponse->getIssuer()) {
            Mage::throwException('Issuer is not specified.');
        }

        if ($logoutResponse->getIssuer()->getValue() != $this->_configSp->getEntityId()) {
            Mage::throwException("SP '{$logoutResponse->getIssuer()->getValue()}' unknown issuer.");
        }
        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutResponse $logoutResponse
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateSignature($logoutResponse)
    {
        /** @var \LightSaml\Model\XmlDSig\SignatureXmlReader $signatureReader */
        $signatureReader = $logoutResponse->getSignature();
        if ($this->_configIdp->isWantLogoutResponseSigned() && !$signatureReader) {
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
     * @param \LightSaml\Model\Protocol\LogoutResponse $logoutResponse
     * @param string|null $requestId
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateRequestId($logoutResponse, $requestId = null)
    {
        if (!$requestId) {
            Mage::throwException('Logout request was not sent.');
        }

        if ($logoutResponse->getInResponseTo() != $requestId) {
            Mage::throwException("The InResponseTo of the Logout Response: {$logoutResponse->getInResponseTo()}, does not match the ID of the Logout request sent by the SP: {$requestId}");
        }
        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutResponse $logoutResponse
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateDestination($logoutResponse)
    {
        if (!$logoutResponse->getDestination()) {
            Mage::throwException('Destination url is not specified.');
        }

        if ($this->_configIdp->getLogoutUrl() != $logoutResponse->getDestination()) {
            Mage::throwException("The Destination of the Logout Response {$logoutResponse->getDestination()}, does not match with current page {$this->_configIdp->getLogoutUrl()}.");
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutResponse $logoutResponse
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _validateStatus($logoutResponse)
    {
        if (!$logoutResponse->getStatus()) {
            Mage::throwException('Response status is not specified.');
        }

        if (!$logoutResponse->getStatus()->isSuccess()) {
            Mage::throwException($this->_getErrorStatusMsg($logoutResponse->getStatus()));
        }

        return true;
    }

    /**
     * @param \LightSaml\Model\Protocol\Status $status
     * @return string
     */
    protected function _getErrorStatusMsg($status)
    {
        $explodedCode = explode(':', $status->getStatusCode());
        $printableCode = array_pop($explodedCode);

        $statusExceptionMsg = 'The status code of the Response was not Success, was ' . $printableCode;
        if ($status->getStatusMessage()) {
            $statusExceptionMsg .= ' -> ' . $status->getStatusMessage();
        }
        return $statusExceptionMsg;
    }
}
