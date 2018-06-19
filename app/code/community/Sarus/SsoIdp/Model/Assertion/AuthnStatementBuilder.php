<?php

use LightSaml\SamlConstants;

class Sarus_SsoIdp_Model_Assertion_AuthnStatementBuilder
{
    /**
     * @param string $time
     * @return \DateTime
     */
    protected function _createDateTime($time)
    {
        return new \DateTime($time);
    }

    /**
     * @return \LightSaml\Model\Assertion\AuthnStatement
     */
    protected function _createAssertionAuthnStatement()
    {
        return new \LightSaml\Model\Assertion\AuthnStatement();
    }

    /**
     * @return \LightSaml\Model\Assertion\AuthnContext
     */
    protected function _createAssertionAuthnContext()
    {
        return new \LightSaml\Model\Assertion\AuthnContext();
    }

    /**
     * @return Sarus_SsoIdp_Model_Session
     */
    protected function _getSsoSession()
    {
        return Mage::getSingleton('sarus_ssoidp/session');
    }

    /**
     * @return \LightSaml\Model\Assertion\AuthnStatement
     */
    public function build()
    {
        $authnStatement = $this->_createAssertionAuthnStatement();
        $authnStatement->setAuthnInstant($this->_createDateTime('-10 MINUTE'));
        $authnStatement->setSessionIndex($this->_getSsoSession()->getSessionId());
        $authnStatement->setAuthnContext($this->_buildAuthnContext());
        return $authnStatement;
    }

    /**
     * @return \LightSaml\Model\Assertion\AuthnContext
     */
    protected function _buildAuthnContext()
    {
        $authnContext = $this->_createAssertionAuthnContext();
        $authnContext->setAuthnContextClassRef(SamlConstants::AUTHN_CONTEXT_PASSWORD_PROTECTED_TRANSPORT); // TODO
        return $authnContext;
    }
}
