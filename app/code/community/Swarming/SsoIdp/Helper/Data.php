<?php

class Swarming_SsoIdp_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOGOUT_REQUEST_ID = 'logout_request_id';

    /**
     * @param int|string|null $storeId
     * @return \Mage_Core_Model_Store
     */
    public function getFrontendStore($storeId = null)
    {
        return !$storeId || Mage::app()->getStore()->isAdmin()
            ? Mage::app()->getDefaultStoreView()
            : Mage::app()->getStore($storeId);
    }
}
