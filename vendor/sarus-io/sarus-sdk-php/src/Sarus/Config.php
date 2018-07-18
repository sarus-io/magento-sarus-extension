<?php

namespace Sarus;

class Config
{
    const DEFAULT_BASE_URI = 'https://api.sarus.io';
    const DEFAULT_TIMEOUT  = 30;
    const DEFAULT_SSL_VERIFY = true;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var int
     */
    private $timeout;

    /**
     * @var bool
     */
    private $sslVerify;

    /**
     * @param $secret
     * @param string $baseUri
     * @param int $timeout
     * @param bool $sslVerify
     */
    public function __construct(
        $secret,
        $baseUri = self::DEFAULT_BASE_URI,
        $timeout = self::DEFAULT_TIMEOUT,
        $sslVerify = self::DEFAULT_SSL_VERIFY
    ) {
        $this->secret    = $this->filterValidateSecret($secret);
        $this->baseUri   = $this->filterValidateBaseUri($baseUri);
        $this->timeout   = $this->filterValidateTimeout($timeout);
        $this->sslVerify = $this->filterValidateSslVerify($sslVerify);
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data)
    {
        if (empty($data['secret'])) {
            throw new \InvalidArgumentException('Parameter `secret` is required parameter');
        }

        return new self(
            $data['secret'],
            isset($data['baseUri']) ? $data['baseUri'] : self::DEFAULT_BASE_URI,
            isset($data['timeout']) ? $data['timeout'] : self::DEFAULT_TIMEOUT,
            isset($data['sslVerify']) ? $data['sslVerify'] : self::DEFAULT_SSL_VERIFY
        );
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @return string
     */
    public function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @return bool
     */
    public function isSslVerify()
    {
        return $this->sslVerify;
    }

    /**
     * @param string $secret
     * @return string
     */
    private function filterValidateSecret($secret)
    {
        if (!is_string($secret) || !$secret) {
            throw new \InvalidArgumentException('Parameter `secret` should be a non-empty string');
        }

        return $secret;
    }

    /**
     * @param string $baseUri
     * @return string
     */
    private function filterValidateBaseUri($baseUri)
    {
        $baseUri = filter_var((string) $baseUri, FILTER_VALIDATE_URL);
        if (false === $baseUri) {
            throw new \InvalidArgumentException('Parameter `baseUri` should be a valid url');
        };

        return $baseUri;
    }

    /**
     * @param $timeout
     * @return int
     */
    private function filterValidateTimeout($timeout)
    {
        $timeout = (int) $timeout;
        if ($timeout <= 0) {
            throw new \InvalidArgumentException('Parameter `timeout` should be integer value greater than 0');
        }

        return $timeout;
    }

    /**
     * @param $sslVerify
     * @return bool
     */
    private function filterValidateSslVerify($sslVerify)
    {
        return (bool) $sslVerify;
    }
}
