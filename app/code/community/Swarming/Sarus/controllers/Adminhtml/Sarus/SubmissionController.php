<?php

class Swarming_Sarus_Adminhtml_Sarus_SubmissionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var Swarming_Sarus_Model_Submission_Manager
     */
    protected $_submissionManager;

    protected function _construct()
    {
        $this->_submissionManager = Mage::getModel('swarming_sarus/submission_manager');
        parent::_construct();
    }

    /**
     * @return Mage_Admin_Model_Session
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
        return $this->_getAdminSession()->isAllowed('report/swarming_sarus');
    }

    public function indexAction()
    {
        $this->_title($this->__('Rise LMS Submissions'));

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
            $processedSubmissions = $this->_submissionManager->resendByIds($submissionIds);
            $this->_getSession()->addSuccess($this->__('%s submission(s) were processed.', $processedSubmissions));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addException($e, $this->__('An error occurred while resending submissions.'));
        }

        $this->_redirect('*/*/index');
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');

        try {
            if (empty($ids)) {
                Mage::throwException($this->__('Please select Error Log(s).'));
            }

            /** @var Swarming_Sarus_Model_Submission $submission */
            $submission = Mage::getModel('swarming_sarus/submission');

            foreach ($ids as $id) {
                $submission->load($id);
                $submission->delete();
            }

            $this->_getSession()->addSuccess($this->__('Total of %d record(s) have been deleted.', count($ids)));
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError(
                $this->__('An error occurred while mass deleting items. Please review log and try again.')
            );
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
     * @return Swarming_Sarus_Block_Adminhtml_ErrorLog_Grid
     */
    protected function _createGridBlock()
    {
        return $this->getLayout()->createBlock('swarming_sarus/adminhtml_errorLog_grid');
    }
}
