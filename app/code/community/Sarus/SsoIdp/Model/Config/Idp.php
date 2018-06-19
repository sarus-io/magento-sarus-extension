<?php

class Sarus_SsoIdp_Model_Config_Idp
{
    const XML_PATH_ENABLED = 'sarus_ssoidp/general/enabled';
    const XML_PATH_ENABLED_SLO = 'sarus_ssoidp/general/enabled_slo';

    const XML_PATH_ENTITY_ID = 'sarus_ssoidp/general/entity_id';

    const XML_PATH_METADATA_URL = 'sarus_ssoidp/general/metadata_url';
    const XML_PATH_SINGLE_SING_ON_URL = 'sarus_ssoidp/general/single_sing_on_url';
    const XML_PATH_SINGLE_LOGOUT_URL = 'sarus_ssoidp/general/single_logout_url';

    const XML_PATH_ALLOWED_SECONDS_SKEW = 'sarus_ssoidp/general/allowed_seconds_skew';


    const XML_PATH_WANT_METADATA_SIGNED = 'sarus_ssoidp/security/metadata_signed';
    const XML_PATH_WANT_AUTHN_SIGNED = 'sarus_ssoidp/security/want_authn_signed';
    const XML_PATH_WANT_LOGOUT_REQUEST_SIGNED = 'sarus_ssoidp/security/want_logout_request_signed';
    const XML_PATH_WANT_LOGOUT_RESPONSE_SIGNED = 'sarus_ssoidp/security/want_logout_response_signed';

    const XML_PATH_ASSERTION_ENCRYPTED = 'sarus_ssoidp/security/assertion_encrypted';
    const XML_PATH_ASSERTION_SIGNED = 'sarus_ssoidp/security/assertion_signed';
    const XML_PATH_MESSAGES_SIGNED = 'sarus_ssoidp/security/messages_signed';

    const XML_PATH_SIGNATURE_ALGORITHM = 'sarus_ssoidp/security/signature_algorithm';
    const XML_PATH_DIGEST_ALGORITHM = 'sarus_ssoidp/security/digest_algorithm';
    const XML_PATH_ENCRYPTED_METHOD_KEY = 'sarus_ssoidp/security/encrypted_method_key';
    const XML_PATH_ENCRYPTED_METHOD_DATA = 'sarus_ssoidp/security/encrypted_method_data';

    const XML_PATH_PRIVATE_KEY = 'sarus_ssoidp/credentials/private_key';
    const XML_PATH_CERT = 'sarus_ssoidp/credentials/cert';

    const XML_PATH_TECHNICAL_CONTACT_GIVEN_NAME = 'sarus_ssoidp/contact/technical_contact_given_name';
    const XML_PATH_TECHNICAL_CONTACT_EMAIL = 'sarus_ssoidp/contact/technical_contact_email';

    const XML_PATH_SUPPORT_CONTACT_GIVEN_NAME = 'sarus_ssoidp/contact/support_contact_given_name';
    const XML_PATH_SUPPORT_CONTACT_EMAIL = 'sarus_ssoidp/contact/support_contact_email';

    /**
     * @var Sarus_SsoIdp_Helper_Data
     */
    protected $_helper;

    public function __construct()
    {
        $this->_helper = Mage::helper('sarus_ssoidp');
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isEnabledSlo($storeId = null)
    {
        return $this->isEnabled($storeId) && Mage::getStoreConfigFlag(self::XML_PATH_ENABLED_SLO, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getEntityId($storeId = null)
    {
        return $this->getMetadataUrl($storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getMetadataUrl($storeId = null)
    {
        return $this->_helper->getFrontendStore($storeId)->getUrl(Mage::getStoreConfig(self::XML_PATH_METADATA_URL), array('_secure' => true));
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getSingleSingOnUrl($storeId = null)
    {
        return $this->_helper->getFrontendStore($storeId)->getUrl(Mage::getStoreConfig(self::XML_PATH_SINGLE_SING_ON_URL), array('_secure' => true));
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getLogoutUrl($storeId = null)
    {
        return $this->_helper->getFrontendStore($storeId)->getUrl(Mage::getStoreConfig(self::XML_PATH_SINGLE_LOGOUT_URL), array('_secure' => true));
    }

    /**
     * @return string|int
     */
    public function getAllowedSecondsSkew()
    {
        return Mage::getStoreConfig(self::XML_PATH_ALLOWED_SECONDS_SKEW);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isMetadataSigned($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_WANT_METADATA_SIGNED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isWantAuthnSigned($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_WANT_AUTHN_SIGNED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isWantLogoutRequestSigned($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_WANT_LOGOUT_REQUEST_SIGNED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isWantLogoutResponseSigned($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_WANT_LOGOUT_RESPONSE_SIGNED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isAssertionEncrypted($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ASSERTION_ENCRYPTED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isAssertionSigned($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ASSERTION_SIGNED, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return bool
     */
    public function isMessagesSigned($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_MESSAGES_SIGNED, $storeId);
    }
    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getSignatureAlgorithm($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SIGNATURE_ALGORITHM, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getDigestAlgorithm($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_DIGEST_ALGORITHM, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getEncryptedMethodKey($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ENCRYPTED_METHOD_KEY, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getEncryptedMethodData($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ENCRYPTED_METHOD_DATA, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getPrivateKey($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PRIVATE_KEY, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getCert($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CERT, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getTechnicalContactGivenName($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TECHNICAL_CONTACT_GIVEN_NAME, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getTechnicalContactEmail($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_TECHNICAL_CONTACT_EMAIL, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getSupportContactGivenName($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SUPPORT_CONTACT_GIVEN_NAME, $storeId);
    }

    /**
     * @param int|string|null $storeId
     * @return string
     */
    public function getSupportContactEmail($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_SUPPORT_CONTACT_EMAIL, $storeId);
    }
}
