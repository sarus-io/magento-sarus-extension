<?php

use RobRichards\XMLSecLibs\XMLSecurityKey;

class Swarming_SsoIdp_Model_Config_Source_SignatureAlgorithm
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => XMLSecurityKey::RSA_SHA1, 'label'=> XMLSecurityKey::RSA_SHA1),
            array('value' => XMLSecurityKey::DSA_SHA1, 'label'=> XMLSecurityKey::DSA_SHA1),
            array('value' => XMLSecurityKey::RSA_SHA256, 'label'=> XMLSecurityKey::RSA_SHA256),
            array('value' => XMLSecurityKey::RSA_SHA384, 'label'=> XMLSecurityKey::RSA_SHA384),
            array('value' => XMLSecurityKey::RSA_SHA512, 'label'=> XMLSecurityKey::RSA_SHA512),
        );
    }
}

