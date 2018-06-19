<?php

class Sarus_Sarus_Model_Cron_SendSubmissions
{
    /**
     * @var \Sarus_Sarus_Model_Config_General
     */
    protected $_configGeneral;

    /**
     * @var \Sarus_Sarus_Model_QueueManager
     */
    protected $_queueManager;

    /**
     * @var \Mage_Core_Model_App
     */
    protected $_app;

    public function __construct()
    {
        $this->_configGeneral = Mage::getModel('sarus_sarus/config_general');
        $this->_queueManager = Mage::getModel('sarus_sarus/queueManager');
        $this->_app = Mage::app();
    }

    /**
     * @return void
     */
    public function execute()
    {
        $stores = $this->_app->getStores();

        foreach (array_keys($stores) as  $storeId) {
            if (!$this->_configGeneral->isEnabled($storeId)) {
                continue;
            }

            $this->_queueManager->sendPendingSubmissions($storeId);
        }
    }
}
