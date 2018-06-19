<?php

class Sarus_SsoIdp_Model_Session extends Mage_Core_Model_Session_Abstract
{
    public function __construct()
    {
        $this->init('sarus_sso_idp');
    }

}
