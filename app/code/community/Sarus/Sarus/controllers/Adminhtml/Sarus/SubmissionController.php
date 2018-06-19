<?php

class Sarus_Sarus_Adminhtml_Sarus_SubmissionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var \Sarus_Sarus_Model_Queue
     */
    protected $_queue;

    /**
     * @var \Sarus_Sarus_Model_QueueManager
     */
    protected $_queueManager;

    protected function _construct()
    {
        $this->_queue = Mage::getModel('sarus_sarus/queue');
        $this->_queueManager = Mage::getModel('sarus_sarus/queueManager');
        parent::_construct();
    }

    /**
     * @return \Mage_Admin_Model_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_getAdminSession()->isAllowed('report/sarus_sarus');
    }

    public function indexAction()
    {
        $this->_title($this->__('Sarus Queue'));

        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    public function massSendAction()
    {
        $submissionIds = (array)$this->getRequest()->getPost('ids');

        try {
            if (empty($submissionIds)) {
                Mage::throwException($this->__('Please select Error Log(s).'));
            }
            $processedSubmissions = $this->_queueManager->sendByIds($submissionIds);
            $this->_getSession()->addSuccess($this->__('%s submission(s) were sent successfully.', $processedSubmissions));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addException($e, $this->__('An error occurred while sending submissions.'));
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $submissionIds = $this->getRequest()->getParam('ids');

        try {
            if (empty($submissionIds)) {
                Mage::throwException($this->__('Please select Error Log(s).'));
            }

            $this->_queue->deleteByIds($submissionIds);
            $this->_getSession()->addSuccess($this->__('Submission(s) were deleted successfully.'));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('An error occurred while deleting submissions.'));
            Mage::logException($e);
        }

        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $this->_prepareDownloadResponse('Error Log_export.csv', $this->_createGridBlock()->getCsvFile());
    }

    public function exportXmlAction()
    {
        $this->_prepareDownloadResponse('Error Log_export.xml', $this->_createGridBlock()->getExcelFile());
    }

    /**
     * @return Sarus_Sarus_Block_Adminhtml_Queue_Grid
     */
    protected function _createGridBlock()
    {
        return $this->getLayout()->createBlock('sarus_sarus/adminhtml_queue_grid');
    }
}
