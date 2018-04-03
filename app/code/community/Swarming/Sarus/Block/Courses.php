<?php

class Swarming_Sarus_Block_Courses extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * @var Swarming_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var Swarming_Sarus_Model_Service_Courses
     */
    protected $_courseService;

    /**
     * @var Swarming_Sarus_Model_Service_Token
     */
    protected $_tokenService;

    public function _construct()
    {
        $this->_configGeneral = Mage::getModel('swarming_sarus/config_general');
        $this->_courseService = Mage::getModel('swarming_sarus/service_courses');
        $this->_tokenService  = Mage::getModel('swarming_sarus/service_token');

        return parent::_construct();
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return array
     */
    public function getCustomerCourses()
    {
        return $this->_courseService->getCourses($this->_getCustomerEmail());
    }

    /**
     * @return string
     */
    protected function _getCustomerEmail()
    {
        $customerEmail = $this->_getCustomerSession()->getCustomer()->getEmail();
        return $customerEmail;
    }

    /**
     * @param array $courseData
     * @return string
     */
    public function getCourseImageUrl($courseData)
    {
        return !empty($courseData['image_src'])
            ? $courseData['image_src']
            : $this->getSkinUrl('/images/catalog/product/placeholder/image.jpg');
    }

    /**
     * @param array $courseData
     * @return string
     */
    public function getCourseLink($courseData)
    {
        return $this->_configGeneral->getRiseBaseUrl() . '/e/' . $courseData['course_uuid'];
    }
}
