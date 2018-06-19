<?php

class Sarus_Sarus_Block_Adminhtml_Queue_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('sarus_submission_grid');
        $this->setDefaultSort('submission_time');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return \Sarus_Sarus_Model_Resource_Submission_Collection
     */
    protected function _createSubmissionCollection()
    {
        return Mage::getResourceModel('sarus_sarus/submission_collection');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $submissionCollection = $this->_createSubmissionCollection();
        $submissionCollection->setOrder('entity_id', Sarus_Sarus_Model_Resource_Submission_Collection::SORT_ORDER_ASC);
        $this->setCollection($submissionCollection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => $this->__('Entity Id'),
                'index' => 'entity_id'
            ]
        );

        $this->addColumn(
            'store_id',
            [
                'header' => $this->__('Store View'),
                'index' => 'store_id',
                'type' => 'store',
                'sortable' => false,
                'store_view' => true,
            ]
        );

        $this->addColumn(
            'request',
            [
                'header' => $this->__('Request'),
                'index' => 'request'
            ]
        );


        $this->addColumn(
            'counter',
            [
                'header' => $this->__('Counter'),
                'index' => 'counter'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => $this->__('status'),
                'index' => 'status',
                'type'=>'options',
                'options' => [
                    Sarus_Sarus_Model_Submission::STATUS_PENDING => $this->__('Pending'),
                    Sarus_Sarus_Model_Submission::STATUS_DONE => $this->__('Done'),
                    Sarus_Sarus_Model_Submission::STATUS_FAIL => $this->__('Fain')
                ]
            ]
        );

        $this->addColumn(
            'error_message',
            [
                'header' => $this->__('Last Error Message'),
                'index' => 'error_message'
            ]
        );

        $this->addColumn(
            'creating_time',
            [
                'header' => $this->__('Creating Time'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'creating_time',
            ]
        );

        $this->addColumn(
            'submission_time',
            [
                'header' => $this->__('Submission Time'),
                'align' => 'left',
                'type' => 'datetime',
                'index' => 'submission_time',
            ]
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

        $this->getMassactionBlock()->addItem('resend', [
            'label' => $this->__('Send Submission'),
            'url' => $this->getUrl('*/*/massSend')
        ]);

        $this->getMassactionBlock()->addItem('delete', [
            'label' => $this->__('Delete Submission'),
            'url' => $this->getUrl('*/*/massDelete')
        ]);
    }

    /**
     * @param \Sarus_Sarus_Model_Submission $row
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
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }
}
