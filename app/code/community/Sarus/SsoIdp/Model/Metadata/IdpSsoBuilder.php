<?php

use LightSaml\Model\Metadata\KeyDescriptor;

class Sarus_SsoIdp_Model_Metadata_IdpSsoBuilder
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Sarus_SsoIdp_Helper_IdpCredentials
     */
    protected $_idpCredentials;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_idpCredentials = Mage::helper('sarus_ssoidp/idpCredentials');
    }

    /**
     * @return \LightSaml\Model\Metadata\IdpSsoDescriptor
     */
    protected function _createIdpSsoDescriptor()
    {
        return new \LightSaml\Model\Metadata\IdpSsoDescriptor();
    }

    /**
     * @return \LightSaml\Model\Metadata\SingleSignOnService
     */
    protected function _createSingleSignOnService()
    {
        return new \LightSaml\Model\Metadata\SingleSignOnService();
    }

    /**
     * @return \LightSaml\Model\Metadata\SingleLogoutService
     */
    protected function _createSingleLogoutService()
    {
        return new \LightSaml\Model\Metadata\SingleLogoutService();
    }

    /**
     * @param string $type
     * @param \LightSaml\Credential\X509Certificate $certificate
     * @return \LightSaml\Model\Metadata\KeyDescriptor
     */
    protected function _createKeyDescriptor($type, $certificate)
    {
        return new \LightSaml\Model\Metadata\KeyDescriptor($type, $certificate);
    }

    /**
     * @return \LightSaml\Model\Metadata\IdpSsoDescriptor
     */
    public function build()
    {
        $idpSsoDescriptor = $this->_createIdpSsoDescriptor();

        $idpSsoDescriptor->setWantAuthnRequestsSigned($this->_configIdp->isWantLogoutRequestSigned());

        if ($this->_configIdp->isAssertionEncrypted()) {
            $idpSsoDescriptor->addKeyDescriptor($this->_createKeyDescriptor(KeyDescriptor::USE_ENCRYPTION, $this->_idpCredentials->getCertificate()));
        }
        $idpSsoDescriptor->addKeyDescriptor($this->_createKeyDescriptor(KeyDescriptor::USE_SIGNING, $this->_idpCredentials->getCertificate()));

        $idpSsoDescriptor->addNameIDFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_ENTITY);
        $idpSsoDescriptor->addNameIDFormat(\LightSaml\SamlConstants::NAME_ID_FORMAT_EMAIL);

        if ($this->_configIdp->getSingleSingOnUrl()) {
            $idpSsoDescriptor->addSingleSignOnService($this->buildSingleSignOnService());
        }

        if ($this->_configIdp->getLogoutUrl()) {
            $idpSsoDescriptor->addSingleLogoutService($this->buildSingleLogoutService());
        }

        return $idpSsoDescriptor;
    }

    /**
     * @return \LightSaml\Model\Metadata\SingleSignOnService
     */
    protected function buildSingleSignOnService()
    {
        $service = $this->_createSingleSignOnService();
        $service->setLocation($this->_configIdp->getSingleSingOnUrl());
        $service->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        return $service;
    }

    /**
     * @return \LightSaml\Model\Metadata\SingleLogoutService
     */
    protected function buildSingleLogoutService()
    {
        $service = $this->_createSingleLogoutService();
        $service->setLocation($this->_configIdp->getLogoutUrl());
        $service->setBinding(\LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        return $service;
    }
}
