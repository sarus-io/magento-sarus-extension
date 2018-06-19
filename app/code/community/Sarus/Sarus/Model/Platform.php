<?php

use Sarus_Sarus_Model_Platform_SdkFactory as SdkFactory;

class Sarus_Sarus_Model_Platform
{
    /**
     * @var \Sarus_Sarus_Model_Platform_SdkFactory
     */
    protected $_sdkFactory;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * @var \Sarus\Sdk[]
     */
    protected $_register = [];

    public function __construct()
    {
        $this->_sdkFactory = Mage::getModel('sarus_sarus/platform_sdkFactory');
        $this->_app = Mage::app();
    }

    /**
     * @param int|string|null $storeId
     * @return \Sarus\Sdk
     */
    public function getSdk($storeId = null)
    {
        $storeId = $this->_app->getStore($storeId)->getId();

        if (empty($this->_register[$storeId])) {
            $this->_register[$storeId] = $this->_sdkFactory->create([SdkFactory::CONFIG_STORE => $storeId]);
        }

        return $this->_register[$storeId];
    }

    /**
     * @param \Sarus\Request $sarusRequest
     * @param int|null $storeId
     * @return \Sarus\Response
     */
    public function sendRequest(\Sarus\Request $sarusRequest, $storeId = null)
    {
        return $this->getSdk($storeId)->handleRequest($sarusRequest);
    }
}
