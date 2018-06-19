<?php

class Sarus_SsoIdp_Model_Observer_RegisterVendorAutoload
{
    /**
     * @var bool
     */
    static $_processed = false;

    /**
     * @param \Varien_Event_Observer $observer
     * @return void
     */
    public function execute(Varien_Event_Observer $observer)
    {
        if (false === self::$_processed) {
            self::$_processed = true;
            require_once BP . '/vendor/autoload.php';
        }
    }
}
