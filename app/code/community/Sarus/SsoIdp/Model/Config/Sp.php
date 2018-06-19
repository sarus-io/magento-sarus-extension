<?php

class Sarus_SsoIdp_Model_Config_Sp
{
    const XML_PATH_ENTITY_ID = 'sarus_ssoidp/sp/entity_id';
    const XML_PATH_NAME_ID = 'sarus_ssoidp/sp/name_id';
    const XML_PATH_ASSERTION_CONSUMER_URL = 'sarus_ssoidp/sp/assertion_consumer_url';
    const XML_PATH_ASSERTION_CONSUMER_BINDING = 'sarus_ssoidp/sp/assertion_consumer_binding';
    const XML_PATH_SINGLE_LOGOUT_URL = 'sarus_ssoidp/sp/single_logout_url';
    const XML_PATH_CERT = 'sarus_ssoidp/sp/cert';

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getEntityId($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ENTITY_ID, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getNameId($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_NAME_ID, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getNameIdFormat($storeId = null)
    {
        return Sarus_SsoIdp_Model_Config_Source_NameId::getFormat($this->getNameId($storeId));
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getAssertionConsumerUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ASSERTION_CONSUMER_URL, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getAssertionConsumerBinding($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ASSERTION_CONSUMER_BINDING, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getSingleLogoutUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SINGLE_LOGOUT_URL, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getCert($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CERT, $storeId);
    }
}
