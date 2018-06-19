<?php

class Sarus_SsoIdp_Model_Assertion_EncryptorWriterBuilder
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Sarus_SsoIdp_Helper_SpCredentials
     */
    protected $_spCredentials;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_spCredentials = Mage::helper('sarus_ssoidp/spCredentials');
    }

    /**
     * @return \LightSaml\Model\Assertion\EncryptedAssertionWriter
     */
    protected function _createEncryptedAssertionWriter()
    {

        return new \LightSaml\Model\Assertion\EncryptedAssertionWriter(
            $this->_configIdp->getEncryptedMethodData(),
            $this->_configIdp->getEncryptedMethodKey()
        );
    }

    /**
     * @param \LightSaml\Model\Assertion\Assertion $assertion $assertion
     * @return \LightSaml\Model\Assertion\EncryptedAssertionWriter
     */
    public function build($assertion)
    {
        $encryptedAssertionWriter = $this->_createEncryptedAssertionWriter();
        $encryptedAssertionWriter->encrypt($assertion, $this->_spCredentials->getPublicKey());
        return $encryptedAssertionWriter;
    }
}
