<?php

class Sarus_SsoIdp_Model_Config_CertificateGenerator
{
    /**
     * @param string|null $storeCode
     * @return array
     */
    public function generate($storeCode = null)
    {
        $privateKey = openssl_pkey_new($this->_getPrivateKeyConfiguration());
        $csr = openssl_csr_new($this->_getStoreInformation($storeCode), $privateKey);
        $x509 = openssl_csr_sign($csr, null, $privateKey, 3650, ['digest_alg' => 'sha512']);

        $cert = '';
        openssl_x509_export($x509, $cert);

        $key = '';
        openssl_pkey_export($privateKey, $key, null);

        return [
            'private_key' => $key,
            'certificate' => $cert
        ];
    }

    /**
     * @return array
     */
    protected function _getPrivateKeyConfiguration()
    {
        return [
            'digest_alg' => 'sha512',
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ];
    }

    /**
     * @param string|null $storeCode
     * @return array
     */
    protected function _getStoreInformation($storeCode = null)
    {
        $data['countryName'] = $this->_getConfigValue('general/store_information/merchant_country', $storeCode) ?: 'US';

        $organizationName = $this->_getConfigValue('general/store_information/name', $storeCode);
        if ($organizationName) {
            $data['organizationName'] = $organizationName;
        }

        $emailAddress = $this->_getConfigValue('trans_email/ident_support/email', $storeCode);
        if ($emailAddress) {
            $data['emailAddress'] = $emailAddress;
        }

        return $data;
    }

    /**
     * @param string $path
     * @param string|null $storeCode
     * @return string
     */
    protected function _getConfigValue($path, $storeCode = null)
    {
        return Mage::getStoreConfig($path, $storeCode);
    }
}
