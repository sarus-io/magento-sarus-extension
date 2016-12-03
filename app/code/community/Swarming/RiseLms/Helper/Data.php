<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 12/4/15
 * Time: 1:16 PM
 */ 
class Swarming_RiseLms_Helper_Data extends Mage_Core_Helper_Abstract
{
    // Submission response values
    const SUBMISSION_SUCCESS = 1;

    const SUBMISSION_FAILURE = 0;

    // Set max resubmission count
    const MAX_SUBMISSION_COUNT = 10;

    /**
     * Gets configuration data for the LMS Integration
     * @param $config
     * @return mixed
     */
    public function getRiseLmsConfig($config)
    {
        return Mage::getStoreConfig($this->getRiseLmsConfigPath($config));
    }

    /**
     * Gets configuration data for the LMS Integration
     * @param $config
     * @return string
     */
    public function getRiseLmsConfigPath($config)
    {
        return 'riselms_general/general/' . $config;
    }

    /**
     * Get sudo config values from data helper
     * @return string
     * Deprecated
     */
    public function getBaseUrl()
    {
        return 'http://brainmd.demo.riselms.com';
    }

    /**
     * Get API endpoint
     * @return string
     * deprecated
     */
//    public function getEndPoint()
//    {
//        return '/api/v1/purchase';
//    }

    /**
     * @param $orderId
     * @return string
     * Deprecated
     */
//    public function getCourseUrl()
//    {
//        return 'http://brainmd.demo.riselms.com/api/v1/oneclick';
//    }

    public function getSuccessStatus($orderId)
    {
        $customerSuccessStatus = '';
        $customerRecord = Mage::getModel('swarming_riselms/submissionqueue')->load($orderId);
        if ($customerRecord->getSuccess() === true)
        {
            $customerSuccessStatus = $customerRecord->getSuccess();
        }

        return $customerSuccessStatus;
    }

    public function getLicenseKey($orderId, $success = false)
    {
        $customerLicense = '';
        if ($orderId && $success === true)
        {
            $customerRecord = Mage::getModel('swarming_riselms/submissionqueue')->load($orderId);
            $customerLicense = $customerRecord->getLmsLicense();
        }

        return $customerLicense;
    }

    public function getTimeStamp()
    {
        $date = date_create();
        return $date->getTimestamp();
    }

    public function isDebugAllowed()
    {
        return Mage::getStoreConfigFlag('riselms_general/general/rise_debug_mode');
    }

    public function isMyCoursesAllowed()
    {
        return Mage::getStoreConfigFlag('riselms_general/general/rise_my_courses');
    }

    public function isSingleSignOnAllowed()
    {
        return Mage::getStoreConfigFlag('riselms_singlesignon/single_sign_on/sso_enabled');
    }

    public function riseDebugger($data = array(), $label = '')
    {
        if ($this->isDebugAllowed() === true)
        {
            Mage::log(
                $this->getFormalDebugLabel($label) . print_r($data, true),
                null,
                'riselms_debug.log'
            );
        }
    }

    private function getFormalDebugLabel($label)
    {
        if (isset($label))
        {
            $labelLength = strlen($label);

            if (strpos($label, " ", $labelLength - 1))
            {
                $label = $label . ' ';
            }
        }

        return $label;
    }
}