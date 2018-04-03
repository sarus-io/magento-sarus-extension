 <?php

class Swarming_Sarus_Model_Config_General
{
    const XML_PATH_ENABLED = 'swarming_sarus/general/enabled';

    const XML_PATH_MY_COURSES = 'swarming_sarus/general/my_courses';

    const XML_PATH_BASE_URL = 'swarming_sarus/general/rise_base_url';

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isMyCoursesEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_MY_COURSES, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getRiseBaseUrl($storeId = null)
    {
        return rtrim(Mage::getStoreConfig(self::XML_PATH_BASE_URL, $storeId), '/');
    }
}
