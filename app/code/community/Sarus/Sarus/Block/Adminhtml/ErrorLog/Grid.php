<?php

class Sarus_Sarus_Block_Adminhtml_ErrorLog_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
     * @return Sarus_Sarus_Model_Resource_Submission_Collection
     */
    protected function _createSubmissionCollection()
    {
        return Mage::getResourceModel('sarus_sarus/submission_collection');
    }

    /**
     * @return array
     */
    protected function _getActions()
    {
        return Mage::getModel('sarus_sarus/config_source_apiAction')->toOptionHash();
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
            'api_endpoint',
            array(
                'header' => $this->__('Api Action'),
                'index' => 'api_endpoint',
                'type'     => 'options',
                'sortable' => false,
                'options'  => $this->_getActions(),
                'width'    => 130
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
     * @param Sarus_Sarus_Model_Submission $row
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
