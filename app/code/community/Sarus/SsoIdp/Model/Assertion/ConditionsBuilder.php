<?php

class Sarus_SsoIdp_Model_Assertion_ConditionsBuilder
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
     * @return \LightSaml\Model\Assertion\OneTimeUse
     */
    protected function _createAssertionOneTimeUse()
    {
        return new \LightSaml\Model\Assertion\OneTimeUse();
    }

    /**
     * @return \LightSaml\Model\Assertion\Conditions
     */
    protected function _createAssertionConditions()
    {
        return new \LightSaml\Model\Assertion\Conditions();
    }

    /**
     * @param string $audience
     * @return \LightSaml\Model\Assertion\AudienceRestriction
     */
    protected function _createAudienceRestriction($audience)
    {
        return new \LightSaml\Model\Assertion\AudienceRestriction([$audience]);
    }

    /**
     * @param \LightSaml\Model\Protocol\AuthnRequest $authnRequest
     * @return \LightSaml\Model\Assertion\Conditions
     */
    public function build($authnRequest)
    {
        $assertionConditions = $this->_createAssertionConditions();
        $assertionConditions->setNotBefore($this->_createDateTime('now'));
        $assertionConditions->setNotOnOrAfter($this->_createDateTime('+1 MINUTE')); // TODO
        $assertionConditions->addItem($this->_createAudienceRestriction($authnRequest->getIssuer()->getValue()));
        $assertionConditions->addItem($this->_createAssertionOneTimeUse());
        return $assertionConditions;
    }
}
