<?php

class Sarus_Sarus_Model_Config_Api
{
    const XML_PATH_BASE_URI = 'sarus_sarus/api/base_uri';

    const XML_PATH_AUTH_TOKEN = 'sarus_sarus/api/auth_token';

    const XML_PATH_DEBUG = 'sarus_sarus/api/debug';

    const XML_PATH_LOG_FILENAME = 'sarus_sarus/api/log_filename';

    const XML_PATH_NOTIFICATION_RECIPIENT = 'sarus_sarus/api/notification_recipient';

    const XML_PATH_MAX_TIME_RESEND = 'sarus_sarus/api/max_time_resend';

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getBaseUri($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_BASE_URI, $storeId);
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
     * @param int $storeId
     * @return int
     */
    public function getMaxTimeResend($storeId = null)
    {
        return (int)Mage::getStoreConfig(self::XML_PATH_MAX_TIME_RESEND, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return array
     */
    public function getNotificationRecipients($storeId = null)
    {
        $recipients = Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_RECIPIENT, $storeId);
        return !empty($recipients) ? explode(',', $recipients) : [];
    }

    /**
     * @return string
     */
    public function getNotificationSenderEmail()
    {
        return Mage::getStoreConfig('trans_email/ident_support/email');
    }

    /**
     * @return string
     */
    public function getNotificationSenderName()
    {
        return Mage::getStoreConfig('trans_email/ident_support/name');
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
     * @return string
     */
    public function getLogFilename()
    {
        $fileName = Mage::getStoreConfig(self::XML_PATH_LOG_FILENAME);
        return Mage::getBaseDir('var') . DIRECTORY_SEPARATOR . ltrim($fileName, DIRECTORY_SEPARATOR);
    }
}
