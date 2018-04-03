<?php

class Swarming_RiseLms_Model_Resource_Submission extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('swarming_riselms/riselms_submission', 'entity_id');
    }
}
