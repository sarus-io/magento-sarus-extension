<?php

class Sarus_SsoIdp_Adminhtml_Sarus_IdpController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @var \Sarus_SsoIdp_Model_Config_CertificateGenerator
     */
    protected $_certificateGenerator;

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_certificateGenerator = Mage::getModel('sarus_ssoidp/config_certificateGenerator');
        parent::_construct();
    }

    /**
     * @return void
     */
    public function generateCertificateAction()
    {
        $website = $this->getRequest()->getParam('website', null);

        try {
            $storeCode = empty($website)
                ? Mage::app()->getDefaultStoreView()->getCode()
                : Mage::app()->getWebsite($website)->getDefaultStore()->getCode();
            $certificates = $this->_certificateGenerator->generate($storeCode);

            $response = [
                'status' => 'success',
                'message' => $this->__('Certificates are generated.'),
                'private_key' => $certificates['private_key'],
                'certificate' => $certificates['certificate']
            ];
        } catch (Mage_Core_Exception $e) {
            $response['status'] = 'fail';
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            Mage::logException($e);

            $response['status'] = 'fail';
            $response['message'] = $this->__('Something went wrong, try again.');
        }

        $this->_sendAjaxResponse($response);
    }

    /**
     * @param array $response
     * @return void
     */
    protected function _sendAjaxResponse(array $response)
    {
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(json_encode((object)$response));
    }
}
