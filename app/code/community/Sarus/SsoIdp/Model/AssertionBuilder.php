<?php

class Sarus_SsoIdp_Model_AssertionBuilder
{
    /**
     * @var Sarus_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Sarus_SsoIdp_Model_Assertion_IssuerBuilder
     */
    protected $_issuerBuilder;

    /**
     * @var Sarus_SsoIdp_Model_Assertion_SubjectBuilder
     */
    protected $_subjectBuilder;

    /**
     * @var Sarus_SsoIdp_Model_Assertion_ConditionsBuilder
     */
    protected $_conditionsBuilder;

    /**
     * @var Sarus_SsoIdp_Model_Assertion_AuthnStatementBuilder
     */
    protected $_authnStatementBuilder;

    /**
     * @var Sarus_SsoIdp_Model_Assertion_AttributeStatementBuilder
     */
    protected $_attributeStatementBuilder;

    /**
     * @var Sarus_SsoIdp_Model_SignatureWriterFactory
     */
    protected $_signatureWriterFactory;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('sarus_ssoidp/config_idp');
        $this->_issuerBuilder = Mage::getModel('sarus_ssoidp/assertion_issuerBuilder');
        $this->_subjectBuilder = Mage::getModel('sarus_ssoidp/assertion_subjectBuilder');
        $this->_conditionsBuilder = Mage::getModel('sarus_ssoidp/assertion_conditionsBuilder');
        $this->_authnStatementBuilder = Mage::getModel('sarus_ssoidp/assertion_authnStatementBuilder');
        $this->_attributeStatementBuilder = Mage::getModel('sarus_ssoidp/assertion_attributeStatementBuilder');
        $this->_signatureWriterFactory = Mage::getModel('sarus_ssoidp/signatureWriterFactory');
    }

    /**
     * @return \LightSaml\Model\Assertion\Assertion
     */
    protected function _createAssection()
    {
        return new \LightSaml\Model\Assertion\Assertion();
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return \LightSaml\Model\Assertion\Assertion
     */
    public function build($authnRequest)
    {
        $assertion = $this->_createAssection();
        $assertion->setId(\LightSaml\Helper::generateID());
        $assertion->setIssueInstant(new \DateTime());
        $assertion->setIssuer($this->_issuerBuilder->build());
        $assertion->setSubject($this->_subjectBuilder->build($authnRequest));
        $assertion->setConditions($this->_conditionsBuilder->build($authnRequest));
        $assertion->addItem($this->_attributeStatementBuilder->build());
        $assertion->addItem($this->_authnStatementBuilder->build());

        if ($this->_configIdp->isAssertionSigned()) {
            $assertion->setSignature($this->_signatureWriterFactory->create());
        }
        return $assertion;
    }
}
