<?php

use LightSaml\SamlConstants;

class Swarming_SsoIdp_Model_Assertion_SubjectBuilder
{
    /**
     * @var Swarming_SsoIdp_Model_Assertion_NameIdBuilder
     */
    protected $_assertionNameIdBuilder;

    public function __construct()
    {
        $this->_assertionNameIdBuilder = Mage::getModel('swarming_ssoidp/assertion_nameIdBuilder');
    }

    /**
     * @return \LightSaml\Model\Assertion\Subject
     */
    protected function _createSubject()
    {
        return new \LightSaml\Model\Assertion\Subject();
    }

    /**
     * @return \LightSaml\Model\Assertion\SubjectConfirmation
     */
    protected function _createSubjectConfirmation()
    {
        return new \LightSaml\Model\Assertion\SubjectConfirmation();
    }

    /**
     * @return \LightSaml\Model\Assertion\SubjectConfirmationData
     */
    protected function _createSubjectConfirmationData()
    {
        return new \LightSaml\Model\Assertion\SubjectConfirmationData();
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return \LightSaml\Model\Assertion\Subject
     */
    public function build($authnRequest)
    {
        $subject = $this->_createSubject();
        $subject->setNameID($this->_assertionNameIdBuilder->build());
        $subject->addSubjectConfirmation($this->_buildSubjectConfirmation($authnRequest));
        return $subject;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return \LightSaml\Model\Assertion\SubjectConfirmation
     */
    protected function _buildSubjectConfirmation($authnRequest)
    {
        $subjectConfirmation = $this->_createSubjectConfirmation();
        $subjectConfirmation->setMethod(SamlConstants::CONFIRMATION_METHOD_BEARER); // TODO
        $subjectConfirmation->setSubjectConfirmationData($this->_buildSubjectConfirmationData($authnRequest));
        return $subjectConfirmation;
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return \LightSaml\Model\Assertion\SubjectConfirmationData
     */
    protected function _buildSubjectConfirmationData($authnRequest)
    {
        $subjectConfirmationData = $this->_createSubjectConfirmationData();
        $subjectConfirmationData->setInResponseTo($authnRequest->getId());
        $subjectConfirmationData->setNotOnOrAfter(new \DateTime('+1 MINUTE')); // TODO
        $subjectConfirmationData->setRecipient($authnRequest->getAssertionConsumerServiceURL());
        return $subjectConfirmationData;
    }
}
