<?php

class Sarus_SsoIdp_Model_Config_Source_NameId
{
    const EMAIL = 'email';
    const CUSTOMER_ID = 'id';

    const FORMAT_EMAIL = 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress';
    const FORMAT_ENTITY = 'urn:oasis:names:tc:SAML:2.0:nameid-format:entity';

    /**
     * @var Sarus_SsoIdp_Helper_Data
     */
    protected $_hepler;

    /**
     * @var array
     */
    protected static $_formats = array(
        self::EMAIL => self::FORMAT_EMAIL,
        self::CUSTOMER_ID => self::FORMAT_ENTITY
    );

    public function __construct()
    {
        $this->_hepler = Mage::helper('sarus_ssoidp');
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::EMAIL, 'label'=> $this->_hepler->__('Customer Email')),
            array('value' => self::CUSTOMER_ID, 'label'=> $this->_hepler->__('Customer ID')),
        );
    }

    /**
     * @param string $typeName
     * @return string|null
     */
    public static function getFormat($typeName)
    {
        return isset(self::$_formats[$typeName]) ? self::$_formats[$typeName] : null;
    }
}
