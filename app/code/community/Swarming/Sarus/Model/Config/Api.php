<?php

class Swarming_RiseLms_Model_Config_Api
{
    const XML_PATH_BASE_URL = 'swarming_riselms/api/base_url';

    const XML_PATH_AUTH_TOKEN = 'swarming_riselms/api/auth_token';

    const XML_PATH_DEBUG = 'swarming_riselms/api/debug';

    const XML_PATH_NOTIFICATION_RECIPIENT = 'swarming_riselms/api/notification_recipient';

    const XML_PATH_MAX_TIME_RESEND = 'swarming_riselms/api/max_time_resend';

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getBaseUrl($storeId = null)
    {
        return rtrim(Mage::getStoreConfig(self::XML_PATH_BASE_URL, $storeId), '/');
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getAuthToken($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_AUTH_TOKEN, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isDebug($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_DEBUG, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return array
     */
    public function getNotificationRecipients($storeId = null)
    {
        $recipients = Mage::getStoreConfigFlag(self::XML_PATH_NOTIFICATION_RECIPIENT, $storeId);
        return !empty($recipients) ? explode(',', $recipients) : array();
    }

    /**
     * @param int $storeId
     * @return int
     */
    public function getMaxTimeResend($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_MAX_TIME_RESEND, $storeId);
    }
}
