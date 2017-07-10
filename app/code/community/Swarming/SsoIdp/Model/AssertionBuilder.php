<?php

class Swarming_SsoIdp_Model_AssertionBuilder
{
    /**
     * @var Swarming_SsoIdp_Model_Config_Idp
     */
    protected $_configIdp;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_IssuerBuilder
     */
    protected $_issuerBuilder;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_SubjectBuilder
     */
    protected $_subjectBuilder;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_ConditionsBuilder
     */
    protected $_conditionsBuilder;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_AuthnStatementBuilder
     */
    protected $_authnStatementBuilder;

    /**
     * @var Swarming_SsoIdp_Model_Assertion_AttributeStatementBuilder
     */
    protected $_attributeStatementBuilder;

    /**
     * @var Swarming_SsoIdp_Model_SignatureWriterFactory
     */
    protected $_signatureWriterFactory;

    public function __construct()
    {
        $this->_configIdp = Mage::getModel('swarming_ssoidp/config_idp');
        $this->_issuerBuilder = Mage::getModel('swarming_ssoidp/assertion_issuerBuilder');
        $this->_subjectBuilder = Mage::getModel('swarming_ssoidp/assertion_subjectBuilder');
        $this->_conditionsBuilder = Mage::getModel('swarming_ssoidp/assertion_conditionsBuilder');
        $this->_authnStatementBuilder = Mage::getModel('swarming_ssoidp/assertion_authnStatementBuilder');
        $this->_attributeStatementBuilder = Mage::getModel('swarming_ssoidp/assertion_attributeStatementBuilder');
        $this->_signatureWriterFactory = Mage::getModel('swarming_ssoidp/signatureWriterFactory');
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
