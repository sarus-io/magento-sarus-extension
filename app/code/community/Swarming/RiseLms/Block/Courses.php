<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 1/26/16
 * Time: 1:33 PM
 */

class Swarming_RiseLms_Block_Courses extends Mage_Customer_Block_Account_Dashboard
{
    private $_courseModel;

    private $_tokenModel;

    public function _construct()
    {
        /** @var Swarming_RiseLms_Model_Courses _courseModel */
        $this->_courseModel = Mage::getModel('swarming_riselms/courses');

        /** @var Swarming_RiseLms_Model_Token _tokenModel */
        $this->_tokenModel  = Mage::getModel('swarming_riselms/token');
        return parent::_construct();
    }

    public function getCustomerLmsCourses()
    {
        $customer      = Mage::getSingleton('customer/session')->getCustomer();
        $customerEmail = $customer->getEmail();

        if ($token = $this->_retrieveToken($customerEmail))
        {
            // Add current customers email to request body to get participations
            $this->_courseModel->addCourseData('email', $customerEmail);

            // Call the course model to get a list of all courses that this customer is part of
            $customerParticipations = $this->_courseModel->participationsGet();
        }

        if (isset($customerParticipations['isSuccess']) && $customerParticipations['isSuccess'] && isset($token['isSuccess']) && $token['isSuccess'])
        {
            $customerParticipations['token'] = $token;
            return $customerParticipations;
        } else {
            $customerParticipations['resultMessage'] = "No courses exist.";

            return $customerParticipations;
        }
    }

    private function _retrieveToken($customerEmail)
    {
        // Add current customers email to request body to get participations
        $this->_tokenModel->addTokenData('email', $customerEmail);

        // Call the course model to get a list of all courses that this customer is part of
        $courseToken = $this->_tokenModel->tokenPost();

        return $courseToken;
    }
}