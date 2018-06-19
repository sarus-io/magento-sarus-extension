<?php

class Sarus_SsoIdp_Model_Assertion_IssuerBuilder
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
    }

    /**
     * @return \LightSaml\Model\Assertion\Issuer
     */
    protected function _createAssertionIssuer()
    {
        return new \LightSaml\Model\Assertion\Issuer();
    }

    /**
     * @return \LightSaml\Model\Assertion\Issuer
     */
    public function build()
    {
        $issuer = $this->_createAssertionIssuer();
        $issuer->setValue($this->_configIdp->getEntityId());
        return $issuer;
    }
}
