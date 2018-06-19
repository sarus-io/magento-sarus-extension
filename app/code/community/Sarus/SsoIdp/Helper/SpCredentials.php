<?php

use LightSaml\Credential\KeyHelper;

class Sarus_SsoIdp_Helper_SpCredentials
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Sp
     */
    protected $_configSp;

    public function __construct()
    {
        $this->_configSp = Mage::getModel('sarus_ssoidp/config_sp');
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
     * @return \LightSaml\Credential\X509Certificate
     */
    public function getCertificate($storeId = null)
    {
        return $this->_createCertificate()->loadPem($this->_configSp->getCert($storeId));
    }

    /**
     * @param string|int|null $storeId
     * @return \RobRichards\XMLSecLibs\XMLSecurityKey
     */
    public function getPublicKey($storeId = null)
    {
        return KeyHelper::createPublicKey($this->getCertificate($storeId));
    }
}
