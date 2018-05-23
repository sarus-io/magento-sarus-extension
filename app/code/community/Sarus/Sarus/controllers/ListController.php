<?php

class Sarus_Sarus_ListController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    protected function _construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        parent::_construct();
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return void
     */
    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->_getCustomerSession()->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * @return void
     */
    public function indexAction()
    {
        if (!$this->_configGeneral->isEnabled() || !$this->_configGeneral->isMyCoursesEnabled()) {
            $this->_forward('noRoute');
            return;
        }

        $this->loadLayout();

        /** @var Mage_Page_Block_Html_Head $headBlock */
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('My Courses'));
        }

        $this->renderLayout();
    }
}
