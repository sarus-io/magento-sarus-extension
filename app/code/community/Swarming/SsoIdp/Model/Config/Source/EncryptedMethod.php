<?php

use RobRichards\XMLSecLibs\XMLSecurityKey;

class Swarming_SsoIdp_Model_Config_Source_EncryptedMethod
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => XMLSecurityKey::TRIPLEDES_CBC, 'label'=> XMLSecurityKey::TRIPLEDES_CBC),
            array('value' => XMLSecurityKey::AES128_CBC, 'label'=> XMLSecurityKey::AES128_CBC),
            array('value' => XMLSecurityKey::AES192_CBC, 'label'=> XMLSecurityKey::AES192_CBC),
            array('value' => XMLSecurityKey::AES256_CBC, 'label'=> XMLSecurityKey::AES256_CBC),
            array('value' => XMLSecurityKey::RSA_1_5, 'label'=> XMLSecurityKey::RSA_1_5),
            array('value' => XMLSecurityKey::RSA_SHA1, 'label'=> XMLSecurityKey::RSA_SHA1),
            array('value' => XMLSecurityKey::RSA_SHA256, 'label'=> XMLSecurityKey::RSA_SHA256),
            array('value' => XMLSecurityKey::RSA_SHA384, 'label'=> XMLSecurityKey::RSA_SHA384),
            array('value' => XMLSecurityKey::RSA_SHA512, 'label'=> XMLSecurityKey::RSA_SHA512),
            array('value' => XMLSecurityKey::RSA_OAEP_MGF1P, 'label'=> XMLSecurityKey::RSA_OAEP_MGF1P),
        );
    }
}
