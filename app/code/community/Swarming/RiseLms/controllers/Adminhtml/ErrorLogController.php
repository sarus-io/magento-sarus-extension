<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 2/5/16
 * Time: 3:59 PM
 */

class Swarming_RiseLms_Adminhtml_ErrorLogController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->_addContent($this->getLayout()->createBlock('swarming_riselms/adminhtml_errorLog'));
        $this->renderLayout();
    }

    public function exportCsvAction()
    {
        $fileName = 'Error Log_export.csv';
        $content = $this->getLayout()->createBlock('swarming_riselms/adminhtml_errorLog_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction()
    {
        $fileName = 'Error Log_export.xml';
        $content = $this->getLayout()->createBlock('swarming_riselms/adminhtml_errorLog_grid')->getExcel();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('ids');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select Error Log(s).'));
        } else {
            try {
                foreach ($ids as $id) {
                    $model = Mage::getSingleton('swarming_riselms/submissionqueue')->load($id);
                    $model->delete();
                }

                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) have been deleted.', count($ids))
                );
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addError(
                    Mage::helper('swarming_riselms')->__('An error occurred while mass deleting items. Please review log and try again.')
                );
                Mage::logException($e);
                return;
            }
        }
        $this->_redirect('*/*/index');
    }
}