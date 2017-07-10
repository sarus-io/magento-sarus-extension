<?php

use RobRichards\XMLSecLibs\XMLSecurityDSig;

class Swarming_SsoIdp_Model_Config_Source_DigestAlgorithm
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => XMLSecurityDSig::SHA1, 'label'=> XMLSecurityDSig::SHA1),
            array('value' => XMLSecurityDSig::SHA256, 'label'=> XMLSecurityDSig::SHA256),
            array('value' => XMLSecurityDSig::SHA384, 'label'=> XMLSecurityDSig::SHA384),
            array('value' => XMLSecurityDSig::SHA512, 'label'=> XMLSecurityDSig::SHA512),
            array('value' => XMLSecurityDSig::RIPEMD160, 'label'=> XMLSecurityDSig::RIPEMD160),
        );
    }
}
