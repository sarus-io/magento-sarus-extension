<?php

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use LightSaml\Error\LightSamlException;

class Sarus_SsoIdp_Model_MessageTransporter
{
    /**
     * @return \LightSaml\Context\Profile\MessageContext
     */
    protected function _createMessageContext()
    {
        return new \LightSaml\Context\Profile\MessageContext();
    }

    /**
     * @return \LightSaml\Binding\BindingFactory
     */
    protected function _createBindingFactory()
    {
        return new \LightSaml\Binding\BindingFactory();
    }

    /**
     * @param \LightSaml\Model\Protocol\SamlMessage $message
     * @param string $bindingType
     * @return void
     */
    public function send($message, $bindingType)
    {
        $messageContext = $this->_createMessageContext();
        $messageContext->setMessage($message);

        $bindingFactory = $this->_createBindingFactory();
        $binding = $bindingFactory->create($bindingType);

        $httpResponse = $binding->send($messageContext);
        $httpResponse->send();
    }

    /**
     * @return \LightSaml\Context\Profile\MessageContext
     * @throws Mage_Core_Exception
     */
    public function buildMessageContextFromRequest()
    {
        $request = SymfonyRequest::createFromGlobals();

        try {
            $bindingFactory = $this->_createBindingFactory();
            $binding = $bindingFactory->getBindingByRequest($request);

            $messageContext = $this->_createMessageContext();
            $binding->receive($request, $messageContext);
        } catch (LightSamlException $e) {
            Mage::throwException('Missing SAMLRequest or SAMLResponse parameter');
        }

        return $messageContext;
    }
}
