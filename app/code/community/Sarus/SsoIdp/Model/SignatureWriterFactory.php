<?php

class Sarus_SsoIdp_Model_SignatureWriterFactory
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Sarus_SsoIdp_Helper_IdpCredentials
     */
    protected $_idpCredential;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_idpCredential = Mage::helper('sarus_ssoidp/idpCredentials');
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
