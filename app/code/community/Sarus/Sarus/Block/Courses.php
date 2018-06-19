<?php

class Sarus_Sarus_Block_Courses extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * @var \Sarus_Sarus_Model_Platform
     */
    protected $_platform;

    public function _construct()
    {
        $this->_platform = Mage::getModel('sarus_sarus/platform');
        return parent::_construct();
    }

    /**
     * @return \Mage_Customer_Model_Session
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
        $sarusResponse = $this->_platform->getSdk()->listEnrollments($this->_getCustomerEmail());
        return (array)$sarusResponse->get('data') ?: [];
    }

    /**
     * @return string
     */
    protected function _getCustomerEmail()
    {
        if (!$this->_getCustomerSession()->isLoggedIn()) {
            throw new \RuntimeException('Courses are not available for not logged in customers.');
        }
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
}
