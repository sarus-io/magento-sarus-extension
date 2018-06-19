<?php

class Sarus_SsoIdp_Model_Assertion_AttributeStatementBuilder
{
    const ATTR_EMAIL = 'email';
    const ATTR_FIRST_NAME = 'first_name';
    const ATTR_MIDDLE_NAME = 'middle_name';
    const ATTR_LAST_NAME = 'last_name';
    const ATTR_ADDRESS1 = 'address1';
    const ATTR_ADDRESS2 = 'address2';
    const ATTR_CITY_LOCALITY = 'city_locality';
    const ATTR_STATE_REGION = 'state_region';
    const ATTR_POSTAL_CODE = 'postal_code';
    const ATTR_COUNTRY = 'country';

    /**
     * @var array
     */
    protected $_customerAttributes = array(
        self::ATTR_EMAIL => 'email',
        self::ATTR_FIRST_NAME => 'firstname',
        self::ATTR_MIDDLE_NAME => 'middlename',
        self::ATTR_LAST_NAME => 'lastname',
    );

    /**
     * @var array
     */
    protected $_addressAttributes = array(
        self::ATTR_ADDRESS1 => 'street1',
        self::ATTR_ADDRESS2 => 'street2',
        self::ATTR_CITY_LOCALITY => 'city',
        self::ATTR_STATE_REGION => 'region',
        self::ATTR_POSTAL_CODE => 'postcode',
        self::ATTR_COUNTRY => 'country_id',
    );

    /**
     * @return \LightSaml\Model\Assertion\AttributeStatement
     */
    protected function _createAttributeStatement()
    {
        return new \LightSaml\Model\Assertion\AttributeStatement();
    }

    /**
     * @return \LightSaml\Model\Assertion\Attribute
     */
    protected function _createAttribute()
    {
        return new \LightSaml\Model\Assertion\Attribute();
    }

    /**
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * @return \LightSaml\Model\Assertion\AttributeStatement
     */
    public function build()
    {
        $attributeData = $this->_fetchCustomerData();

        $attributeStatement = $this->_createAttributeStatement();
        foreach ($attributeData as $name => $value) {
            $attributeStatement->addAttribute($this->_buildAttribute($name, $value));
        }
        return $attributeStatement;
    }

    /**
     * @param string $name
     * @param string $value
     * @return \LightSaml\Model\Assertion\Attribute
     */
    protected function _buildAttribute($name, $value)
    {
        $attribute = $this->_createAttribute();
        $attribute->setName($name);
        $attribute->addAttributeValue($value);
        $attribute->setNameFormat('urn:oasis:names:tc:SAML:2.0:attrname-format:basic');
        return $attribute;
    }

    /**
     * @return array
     */
    protected function _fetchCustomerData()
    {
        $attributeData = array();

        $customer = $this->_getCustomerSession()->getCustomer();
        foreach ($this->_customerAttributes as $remoteAttr => $localAttr) {
            $attributeData[$remoteAttr] = $customer->getDataUsingMethod($localAttr);
        }

        $billingAddress = $customer->getPrimaryBillingAddress();
        if ($billingAddress) {
            foreach ($this->_addressAttributes as $remoteAttr => $localAttr) {
                $attributeData[$remoteAttr] = $billingAddress->getDataUsingMethod($localAttr);
            }
        }

        return $attributeData;
    }
}
