<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 2/5/16
 * Time: 4:05 PM
 */
class Swarming_RiseLms_Block_Adminhtml_ErrorLog_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct()
    {
        parent::__construct();
        $this->setId('grid_id');
        // $this->setDefaultSort('COLUMN_ID');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('swarming_riselms/submissionqueue')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
       $this->addColumn('submissionqueue_id',
           array(
               'header'=> $this->__('Submission Id'),
               'width' => '50px',
               'index' => 'submissionqueue_id'
           )
       );
        $this->addColumn('json',
            array(
                'header'=> $this->__('Json'),
                'width' => '50px',
                'index' => 'json'
            )
        );
        $this->addColumn('error_message',
            array(
                'header'=> $this->__('Error Message'),
                'width' => '50px',
                'index' => 'error_message'
            )
        );
        $this->addColumn('counter',
            array(
                'header'=> $this->__('Counter'),
                'width' => '50px',
                'index' => 'counter'
            )
        );
        
        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
       return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    }
