<?php

/**
 * Display Courses list
 */
class Swarming_RiseLms_ListController extends Mage_Core_Controller_Front_Action
{
    public function preDispatch()
    {
        parent::preDispatch();
        // Require logged in customer
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
        }
    }

    /**
     * Customer Dashboard - My Courses Page
     */
    public function indexAction()
    {
        // Load layout from XML
        $this->loadLayout();

        // Set page title for this page
        $this->getLayout()->getBlock('head')->setTitle($this->__('My Courses'));

        // Render the layout
        $this->renderLayout();
    }

}
