<?php

class Sarus_SsoIdp_Model_MetadataBuilder
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Sarus_SsoIdp_Model_Metadata_IdpSsoBuilder
     */
    protected $_idpSsoBuilder;

    /**
     * @var Sarus_SsoIdp_Model_Metadata_ContactPersonsBuilder
     */
    protected $_contactPersonsBuilder;

    /**
     * @var Sarus_SsoIdp_Model_SignatureWriterFactory
     */
    protected $_signatureWriterFactory;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_idpSsoBuilder = Mage::getModel('sarus_ssoidp/metadata_idpSsoBuilder');
        $this->_contactPersonsBuilder = Mage::getModel('sarus_ssoidp/metadata_contactPersonsBuilder');
        $this->_signatureWriterFactory = Mage::getModel('sarus_ssoidp/signatureWriterFactory');
    }

    /**
     * @return \LightSaml\Model\Metadata\EntityDescriptor
     */
    protected function _createMetadataDescriptor()
    {
        return new \LightSaml\Model\Metadata\EntityDescriptor();
    }

    /**
     * @return \LightSaml\Model\Metadata\EntityDescriptor
     */
    public function build()
    {
        $metadataDescriptor = $this->_createMetadataDescriptor();

        $metadataDescriptor->setEntityID($this->_configIdp->getEntityId());

        $metadataDescriptor->addItem($this->_idpSsoBuilder->build());

        foreach ($this->_contactPersonsBuilder->build() as $contactPerson) {
            $metadataDescriptor->addContactPerson($contactPerson);
        }

        if ($this->_configIdp->isMetadataSigned()) {
            $metadataDescriptor->setSignature($this->_signatureWriterFactory->create());
        }

        return $metadataDescriptor;
    }
}
