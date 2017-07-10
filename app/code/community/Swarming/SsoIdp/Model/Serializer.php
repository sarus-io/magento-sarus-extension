<?php

use \LightSaml\Model\SamlElementInterface;

class Swarming_SsoIdp_Model_Serializer
{
    /**
     * @return \LightSaml\Model\Context\SerializationContext
     */
    protected function _createSerializationContext()
    {
        return new \LightSaml\Model\Context\SerializationContext();
    }

    /**
     * @param \LightSaml\Model\SamlElementInterface $samlElement
     * @return string
     */
    public function toXml(SamlElementInterface $samlElement)
    {
        $serializationContext = new \LightSaml\Model\Context\SerializationContext();
        $samlElement->serialize($serializationContext->getDocument(), $serializationContext);
        return $serializationContext->getDocument()->saveXML();
    }
}
