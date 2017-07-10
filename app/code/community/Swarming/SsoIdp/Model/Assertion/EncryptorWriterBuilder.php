<?php

class Swarming_SsoIdp_Model_Assertion_EncryptorWriterBuilder
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Swarming_SsoIdp_Helper_SpCredentials
     */
    protected $_spCredentials;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
        $this->_spCredentials = Mage::helper('swarming_ssoidp/spCredentials');
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
