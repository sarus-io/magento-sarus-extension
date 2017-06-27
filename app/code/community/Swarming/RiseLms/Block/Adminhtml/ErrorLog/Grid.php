<?php

class Swarming_RiseLms_Block_Adminhtml_ErrorLog_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('riselms_submission_grid');
        $this->setDefaultSort('submission_time');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Swarming_RiseLms_Model_Resource_Submission_Collection
     */
    protected function _createSubmissionCollection()
    {
        return Mage::getResourceModel('swarming_riselms/submission_collection');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $this->setCollection($this->_createSubmissionCollection());
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            array(
                'header' => $this->__('Entity Id'),
                'index' => 'entity_id'
            )
        );

        $this->addColumn(
            'store_id',
            array(
                'header' => $this->__('Store View'),
                'index' => 'store_id',
                'type' => 'store',
                'sortable' => false,
                'store_view' => true,
            )
        );

        $this->addColumn(
            'counter',
            array(
                'header' => $this->__('Counter'),
                'index' => 'counter'
            )
        );

        $this->addColumn(
            'success',
            array(
                'header' => $this->__('Success'),
                'index' => 'success',
                'type'=>'options',
                'options' => array('1' => 'Yes', '0' => 'No')
            )
        );

        $this->addColumn(
            'api_method',
            array(
                'header' => $this->__('API Method'),
                'index' => 'api_method'
            )
        );

        $this->addColumn(
            'api_endpoint',
            array(
                'header' => $this->__('API Endpoint'),
                'index' => 'api_endpoint'
            )
        );

        $this->addColumn(
            'json',
            array(
                'header' => $this->__('Submission Data'),
                'index' => 'json'
            )
        );

        $this->addColumn(
            'error_message',
            array(
                'header' => $this->__('Last Error Message'),
                'index' => 'error_message'
            )
        );

        $this->addColumn(
            'submission_time',
            array(
                'header' => $this->__('Submission Time'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'submission_time',
            )
        );

        $this->addExportType('*/*/exportCsv', $this->__('CSV'));
        $this->addExportType('*/*/exportXml', $this->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('ids');

        $this->getMassactionBlock()->addItem('resend', array(
            'label' => $this->__('Send Submission'),
            'url' => $this->getUrl('*/*/massSend')
        ));

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => $this->__('Delete Submission'),
            'url' => $this->getUrl('*/*/massDelete')
        ));
    }

    /**
     * @param Swarming_RiseLms_Model_Submission $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return '#';
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
