<?php

class Sarus_SsoIdp_IdpController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var \Sarus_SsoIdp_Model_AuthnRequestValidator
     */
    protected $_authnRequestValidator;

    /**
     * @var Sarus_SsoIdp_Model_AuthnResponseBuilder
     */
    protected $_authnResponseBuilder;

    /**
     * @var Sarus_SsoIdp_Model_LogoutRequestValidator
     */
    protected $_logoutRequestValidator;

    /**
     * @var Sarus_SsoIdp_Model_LogoutResponseBuilder
     */
    protected $_logoutResponseBuilder;

    /**
     * @var Sarus_SsoIdp_Model_LogoutResponseValidator
     */
    protected $_logoutResponseValidator;

    /**
     * @var Sarus_SsoIdp_Model_MetadataBuilder
     */
    protected $_metadataBuilder;

    /**
     * @var Sarus_SsoIdp_Model_Serializer
     */
    protected $_serializer;

    /**
     * @var Sarus_SsoIdp_Model_MessageTransporter
     */
    protected $_messageTransporter;

    /**
     * @var Mage_Customer_Helper_Data
     */
    protected $_helperCustomer;

    protected function _construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');

        $this->_authnRequestValidator = Mage::getModel('sarus_ssoidp/authnRequestValidator');
        $this->_authnResponseBuilder = Mage::getModel('sarus_ssoidp/authnResponseBuilder');

        $this->_logoutRequestValidator = Mage::getModel('sarus_ssoidp/logoutRequestValidator');
        $this->_logoutResponseBuilder = Mage::getModel('sarus_ssoidp/logoutResponseBuilder');

        $this->_logoutResponseValidator = Mage::getModel('sarus_ssoidp/logoutResponseValidator');

        $this->_metadataBuilder = Mage::getModel('sarus_ssoidp/metadataBuilder');

        $this->_serializer = Mage::getModel('sarus_ssoidp/serializer');
        $this->_messageTransporter = Mage::getModel('sarus_ssoidp/messageTransporter');

        $this->_helperCustomer = Mage::helper('customer');
        parent::_construct();
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return Sarus_SsoIdp_Model_Session
     */
    protected function _getSsoSession()
    {
        return Mage::getSingleton('sarus_ssoidp/session');
    }

    /**
     * @param string $action
     * @return string
     */
    public function getActionMethodName($action)
    {
        $action = $this->_configIdp->isEnabled() ? $action : 'noroute';
        return parent::getActionMethodName($action);
    }

    public function signonAction()
    {
        try {
            $authnRequest = $this->_messageTransporter->buildMessageContextFromRequest()->asAuthnRequest();
            if (!$authnRequest) {
                Mage::throwException('No Authn Request.');
            }
            $this->_authnRequestValidator->validate($authnRequest);
        } catch (\Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            Mage::logException($e);
            $this->_redirect('customer/account/');
            return;
        }

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $this->_authnResponse($authnRequest);
        } else {
            $this->_getSsoSession()->setData($authnRequest->getID(), $authnRequest);
            $this->_redirect('*/*/login', array('authn_id' => $authnRequest->getID(), '_secure' => true));
        }
    }

    public function loginAction()
    {
        $authnId = $this->getRequest()->getParam('authn_id');

        /** @var \LightSaml\Model\Protocol\AuthnRequest $authRequest */
        $authRequest = $this->_getSsoSession()->getData($authnId);
        if (!$authRequest instanceof \LightSaml\Model\Protocol\AuthnRequest) {
            $this->_redirect('customer/account/');
            return;
        }

        $customerSession = $this->_getCustomerSession();
        if ($customerSession->isLoggedIn()) {
            $this->_authnResponse($authRequest);
            return;
        }


        $this->loadLayout();

        $this->_initLayoutMessages('customer/session');

        /** @var Sarus_SsoIdp_Block_Login $block */
        $block = $this->getLayout()->getBlock('sso.idp.login');
        if ($block) {
            $block->setAuthnId($authnId);
        }

        $this->renderLayout();
    }

    public function loginPostAction()
    {
        $authnId = $this->getRequest()->getParam('authn_id');

        /** @var \LightSaml\Model\Protocol\AuthnRequest $authRequest */
        $authRequest = $this->_getSsoSession()->getData($authnId);
        if (!$authRequest instanceof \LightSaml\Model\Protocol\AuthnRequest) {
            $this->_redirect('customer/account/');
            return;
        }

        $customerSession = $this->_getCustomerSession();
        if ($customerSession->isLoggedIn()) {
            $this->_authnResponse($authRequest);
            return;
        }

        if (!$this->_validateFormKey()) {
            $this->_redirect('customer/account/');
            return;
        }

        if ($this->getRequest()->isPost()) {
            $login = (array)$this->getRequest()->getParam('login');

            if (empty($login['username']) || empty($login['password'])) {
                $customerSession->addError($this->_helperCustomer->__('Login and password are required.'));
            } else {
                $this->_authenticate($login['username'], $login['password']);
            }
        }

        $customerSession = $this->_getCustomerSession();
        if (!$customerSession->isLoggedIn()) {
            $this->_redirect('*/*/login', array('authn_id' => $authnId, '_secure' => true));
            return;
        }

        /** @var \LightSaml\Model\Protocol\AuthnRequest $authRequest */
        $authRequest = $this->_getSsoSession()->getData($authnId);
        if ($authRequest instanceof \LightSaml\Model\Protocol\AuthnRequest) {
            $this->_authnResponse($authRequest);
            return;
        }

        $this->_redirect('customer/account/');
    }

    /**
     * @param string $username
     * @param string $password
     * @return void
     */
    protected function _authenticate($username, $password)
    {
        $customerSession = $this->_getCustomerSession();

        try {
            $customerSession->login($username, $password);
        } catch (Mage_Core_Exception $e) {
            switch ($e->getCode()) {
                case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED:
                    $value = $this->_helperCustomer->getEmailConfirmationUrl($username);
                    $message = $this->_helperCustomer->__('This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value);
                    break;
                case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD:
                default:
                    $message = $e->getMessage();
            }
            $customerSession->addError($message);
            $customerSession->setUsername($username);
        } catch (Exception $e) {
            // Mage::logException($e); // PA DSS violation: this exception log can disclose customer password
        }
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return void
     * @throws \Exception
     */
    protected function _authnResponse($authnRequest)
    {
        try {
            $authnResponse = $this->_authnResponseBuilder->build($authnRequest);
            $this->_messageTransporter->send($authnResponse, \LightSaml\SamlConstants::BINDING_SAML2_HTTP_POST);
            exit;
        } catch (\Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            Mage::logException($e);
            $this->_redirect('customer/account/');
            return;
        }
    }

    public function logoutAction()
    {
        if (!$this->_configIdp->isEnabledSlo()) {
            $this->_forward('noroute');
            return;
        }

        try {
            $messageContext = $this->_messageTransporter->buildMessageContextFromRequest();
        } catch (\Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            Mage::logException($e);
            $this->_redirect('customer/account/');
            return;
        }

        if ($messageContext->asLogoutResponse()) {
            $this->_processLogoutResponse($messageContext->asLogoutResponse());
            return;
        } elseif ($messageContext->asLogoutRequest()) {
            $this->_processLogoutRequest($messageContext->asLogoutRequest());
            return;
        }

        if (Mage::getIsDeveloperMode()) {
            Mage::throwException('Missing SAMLRequest or SAMLResponse parameter.');
        }
        $this->_redirect('customer/account/');
        return;
    }

    /**
     * @param \LightSaml\Model\Protocol\LogoutResponse $logoutResponse
     * @return void
     * @throws \Exception
     */
    protected function _processLogoutResponse($logoutResponse)
    {
        try {
            $requestId = $this->_getSsoSession()->getData(Sarus_SsoIdp_Helper_Data::LOGOUT_REQUEST_ID);
            $this->_logoutResponseValidator->validate($logoutResponse, $requestId);
        } catch (\Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            Mage::logException($e);
            $this->_redirect('customer/account/');
            return;
        }

        $this->_getSsoSession()->clear();
        $this->_getCustomerSession()->logout();
        $this->_getCustomerSession()->renewSession();

        $this->_redirect('customer/account/logoutSuccess');
    }

    /**
     * @param $logoutRequest
     * @return void
     * @throws \Exception
     */
    protected function _processLogoutRequest($logoutRequest)
    {
        try {
            $customer = $this->_getCustomerSession()->isLoggedIn() ? $this->_getCustomerSession()->getCustomer() : null;
            $this->_logoutRequestValidator->validate($logoutRequest, $customer);
        } catch (\Exception $e) {
            if (Mage::getIsDeveloperMode()) {
                throw $e;
            }
            Mage::logException($e);
            $this->_redirect('customer/account/');
            return;
        }

        $this->_getSsoSession()->clear();
        $this->_getCustomerSession()->logout();
        $this->_getCustomerSession()->renewSession();

        $logoutResponse = $this->_logoutResponseBuilder->build($logoutRequest);
        $this->_messageTransporter->send($logoutResponse, \LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        exit;
    }

    /**
     * @return void
     */
    public function metadataAction()
    {
        $metadataDescriptor = $this->_metadataBuilder->build();
        $metadataXml = $this->_serializer->toXml($metadataDescriptor);

        $this->getResponse()->setHeader('Content-Type', 'application/xml');
        $this->getResponse()->setBody($metadataXml);
    }
}
