<?php

class Swarming_SsoIdp_Model_SignatureWriterFactory
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Swarming_SsoIdp_Helper_IdpCredentials
     */
    protected $_idpCredential;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
        $this->_idpCredential = Mage::helper('swarming_ssoidp/idpCredentials');
    }

    /**
     * @return \LightSaml\Model\XmlDSig\SignatureWriter
     */
    public function create()
    {
        return new \LightSaml\Model\XmlDSig\SignatureWriter(
            $this->_idpCredential->getCertificate(),
            $this->_idpCredential->getPrivateKey(),
            $this->_configIdp->getDigestAlgorithm()
        );
    }
}
