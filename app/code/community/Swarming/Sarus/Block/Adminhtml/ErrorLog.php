<?php

class Swarming_Sarus_Block_Adminhtml_ErrorLog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * @return void
     */
    public function _construct()
    {
        $this->_blockGroup = 'swarming_sarus';
        $this->_controller = 'adminhtml_errorLog';
        $this->_headerText = $this->__('Sarus Failed Submission');
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_removeButton('add');
        return parent::_prepareLayout();
    }
}
