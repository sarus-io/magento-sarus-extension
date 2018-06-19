<?php

use \LightSaml\Model\SamlElementInterface;

class Sarus_SsoIdp_Model_Serializer
{
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
