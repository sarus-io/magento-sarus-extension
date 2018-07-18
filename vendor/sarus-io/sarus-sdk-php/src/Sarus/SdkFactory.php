<?php

namespace Sarus;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Sarus\Client\JsonGuzzleClient;

class SdkFactory
{
    /**
     * @param Config $config
     * @return Sdk
     */
    public function create(Config $config)
    {
        $guzzleClient = $this->createGuzzleClient($config);
        return $this->createWithGuzzleClient($guzzleClient);
    }

    /**
     * Creates Sdk using logger and guzzle message formatter
     * @param Config $config
     * @param LoggerInterface $logger
     * @param string $logFormat
     * @param string $logLevel
     * @return Sdk
     */
    public function createWithLogger(
        Config $config,
        LoggerInterface $logger,
        $logFormat = MessageFormatter::CLF,
        $logLevel = LogLevel::INFO
    ) {
        $handler = HandlerStack::create();
        $handler->push(Middleware::log($logger, new MessageFormatter($logFormat), $logLevel));
        $guzzleClient = $this->createGuzzleClient($config, $handler);

        return $this->createWithGuzzleClient($guzzleClient);
    }

    /**
     * @param Config $config
     * @return \GuzzleHttp\Client
     */
    protected function createGuzzleClient(Config $config, HandlerStack $handler = null)
    {
        $config = $this->createGuzzleConfig($config);

        if ($handler) {
            $config['handler'] = $handler;
        }

        return new \GuzzleHttp\Client($config);
    }

    /**
     * @param Config $config
     * @return array|Config
     */
    protected function createGuzzleConfig(Config $config)
    {
        $config =  [
            'base_uri'        => $config->getBaseUri(),
            'timeout'         => $config->getTimeout(),
            'verify'          => $config->isSslVerify(),
            'allow_redirects' => false,
            'cookies'         => false,
            'headers' => [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer ' . $config->getSecret()
            ]
        ];

        return $config;
    }

    /**
     * @param \GuzzleHttp\Client $guzzleClient
     * @return Sdk
     */
    private function createWithGuzzleClient(\GuzzleHttp\Client $guzzleClient)
    {
        return new Sdk(new JsonGuzzleClient($guzzleClient));
    }
}
