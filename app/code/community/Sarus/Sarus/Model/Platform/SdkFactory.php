<?php

use Sarus\Config as SarusConfig;
use Sarus\SdkFactory as SarusSdkFactory;
use Monolog\Logger;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;

class Sarus_Sarus_Model_Platform_SdkFactory
{
    const CONFIG_STORE = 'store';
    const CONFIG_SECRET = 'secret';
    const CONFIG_BASE_URI = 'baseUri';
    const CONFIG_TIMEOUT = 'timeout';
    const CONFIG_SSL_VERIFY = 'sslVerify';

    /**
     * @var \Sarus_Sarus_Model_Config_Api
     */
    protected $_configApi;

    /**
     * @var string
     */
    protected $messageFormat = "{method} - {uri}\nRequest body: {req_body}\n{code} {phrase}\nResponse body: {res_body}\n{error}\n";

    public function __construct()
    {
        $this->_configApi = Mage::getModel('sarus_sarus/config_api');
    }

    /**
     * $config = [
     *     SdkFactory::CONFIG_STORE,
     *     SdkFactory::CONFIG_SECRET,
     *     SdkFactory::CONFIG_BASE_URI,
     *     SdkFactory::CONFIG_TIMEOUT,
     *     SdkFactory::CONFIG_SSL_VERIFY,
     * ]
     *
     * @param string[] $config
     * @return \Sarus\Sdk
     */
    public function create(array $config = [])
    {
        $store = !empty($config[self::CONFIG_STORE]) ? $config[self::CONFIG_STORE] : null;
        unset($config[self::CONFIG_STORE]);

        $sdkConfig = array_merge(
            [
                self::CONFIG_SECRET => $this->_configApi->getAuthToken($store),
            ],
            $config
        );

        $config = SarusConfig::fromArray($sdkConfig);
        $factory = new SarusSdkFactory();

        return $this->_configApi->isDebug($store)
            ? $factory->createWithLogger($config, $this->_creteLogger(), $this->messageFormat)
            : $factory->create($config);
    }

    /**
     * @return \Monolog\Logger
     */
    protected function _creteLogger()
    {
        $logHandler = new RotatingFileHandler($this->_configApi->getLogFilename());
        $logHandler->setFilenameFormat('{filename}-{date}', 'Y-m');
        $logHandler->setFormatter(new LineFormatter(null, null, true, true));
        return new Logger('Logger', [$logHandler]);
    }
}
