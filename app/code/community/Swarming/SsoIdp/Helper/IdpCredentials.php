<?php

use LightSaml\Credential\KeyHelper;

class Swarming_SsoIdp_Helper_IdpCredentials
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
    }

    /**
     * @return \LightSaml\Credential\X509Certificate
     */
    protected function _createCertificate()
    {
        return new \LightSaml\Credential\X509Certificate();
    }

    /**
     * @param string|int|null $storeId
     * @return string
     */
    protected function _getCert($storeId = null)
    {
        return $this->_configIdp->getCert($storeId);
    }

    /**
     * @param string|int|null $storeId
     * @return string
     */
    protected function _getPrivateKey($storeId = null)
    {
        return $this->_configIdp->getPrivateKey($storeId);
    }

    /**
     * @param string|int|null $storeId
     * @return \LightSaml\Credential\X509Certificate
     */
    public function getCertificate($storeId = null)
    {
        return $this->_createCertificate()->loadPem($this->_getCert($storeId));
    }

    /**
     * @param string|int|null $storeId
     * @return \RobRichards\XMLSecLibs\XMLSecurityKey
     */
    public function getPublicKey($storeId = null)
    {
        return KeyHelper::createPublicKey($this->getCertificate($storeId));
    }

    /**
     * @param string|int|null $storeId
     * @return \RobRichards\XMLSecLibs\XMLSecurityKey
     */
    public function getPrivateKey($storeId = null)
    {
        return KeyHelper::createPrivateKey($this->_getPrivateKey($storeId), '', false, $this->_configIdp->getSignatureAlgorithm());
    }
}
