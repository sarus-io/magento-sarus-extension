<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 2/5/16
 * Time: 4:05 PM
 */
class Swarming_RiseLms_Block_Adminhtml_ErrorLog extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct()
    {
        $this->_blockGroup      = 'swarming_riselms';
        $this->_controller      = 'adminhtml_errorLog';
        $this->_headerText      = 'Rise LMS Failed Submission';
        // $this->_addButtonLabel  = $this->__('Add Button Label');
        parent::__construct();
        $this->_removeButton('add');
    }

    public function getCreateUrl()
    {
        return $this->getUrl('*/*/new');
    }

}

