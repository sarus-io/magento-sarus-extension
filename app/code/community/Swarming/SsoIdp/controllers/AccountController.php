<?php

require_once 'Mage/Customer/controllers/AccountController.php';

class Swarming_SsoIdp_AccountController extends Mage_Customer_AccountController
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Swarming_SsoIdp_Model_Config_Sp
     */
    protected $_configSp;

    /**
     * @var Swarming_SsoIdp_Model_LogoutRequestBuilder
     */
    protected $_logoutRequestBuilder;

    /**
     * @var Swarming_SsoIdp_Model_MessageTransporter
     */
    protected $_messageTransporter;

    protected function _construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
        $this->_configSp = Mage::getModel('swarming_ssoidp/config_sp');
        $this->_logoutRequestBuilder = Mage::getModel('swarming_ssoidp/logoutRequestBuilder');
        $this->_messageTransporter = Mage::getModel('swarming_ssoidp/messageTransporter');
        parent::_construct();
    }

    /**
     * @return Swarming_SsoIdp_Model_Session
     */
    protected function _getSsoSession()
    {
        return Mage::getSingleton('swarming_ssoidp/session');
    }

    /**
     * @return void
     */
    public function logoutAction()
    {
        if (!$this->_configIdp->isEnabledSlo() || !$this->_configSp->getSingleLogoutUrl()) {
            parent::logoutAction();
            return;
        }

        $logoutResponse = $this->_logoutRequestBuilder->build();
        $this->_getSsoSession()->setData(Swarming_SsoIdp_Helper_Data::LOGOUT_REQUEST_ID, $logoutResponse->getID());
        $this->_messageTransporter->send($logoutResponse, \LightSaml\SamlConstants::BINDING_SAML2_HTTP_REDIRECT);
        exit;
    }
}
