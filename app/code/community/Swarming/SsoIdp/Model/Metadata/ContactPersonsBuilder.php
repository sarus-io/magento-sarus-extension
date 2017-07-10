<?php

use LightSaml\Model\Metadata\ContactPerson;

class Swarming_SsoIdp_Model_Metadata_ContactPersonsBuilder
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
     * @return \LightSaml\Model\Metadata\ContactPerson
     */
    protected function _createContactPerson()
    {
        return new \LightSaml\Model\Metadata\ContactPerson();
    }

    /**
     * @return \LightSaml\Model\Metadata\ContactPerson[]
     */
    public function build()
    {
        $contactPerson = array();

        if ($this->_configIdp->getTechnicalContactGivenName() || $this->_configIdp->getTechnicalContactEmail()) {
            $contactPerson[] = $this->buildTechnicalContact();
        }

        if ($this->_configIdp->getSupportContactGivenName() || $this->_configIdp->getSupportContactEmail()) {
            $contactPerson[] = $this->buildSupportContact();
        }
        return $contactPerson;
    }

    /**
     * @return \LightSaml\Model\Metadata\ContactPerson
     */
    protected function buildTechnicalContact()
    {
        $contactPerson = $this->_createContactPerson();
        $contactPerson->setContactType(ContactPerson::TYPE_TECHNICAL);

        if ($this->_configIdp->getTechnicalContactGivenName()) {
            $contactPerson->setGivenName($this->_configIdp->getTechnicalContactGivenName());
        }

        if ($this->_configIdp->getTechnicalContactEmail()) {
            $contactPerson->setEmailAddress($this->_configIdp->getTechnicalContactEmail());
        }

        return $contactPerson;
    }

    /**
     * @return \LightSaml\Model\Metadata\ContactPerson
     */
    protected function buildSupportContact()
    {
        $contactPerson = $this->_createContactPerson();
        $contactPerson->setContactType(ContactPerson::TYPE_SUPPORT);

        if ($this->_configIdp->getSupportContactGivenName()) {
            $contactPerson->setGivenName($this->_configIdp->getSupportContactGivenName());
        }

        if ($this->_configIdp->getSupportContactEmail()) {
            $contactPerson->setEmailAddress($this->_configIdp->getSupportContactEmail());
        }

        return $contactPerson;
    }
}
