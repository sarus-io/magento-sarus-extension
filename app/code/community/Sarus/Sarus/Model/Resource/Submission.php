<?php

class Swarming_Sarus_Model_Resource_Submission extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('swarming_sarus/sarus_submission', 'entity_id');
    }
}
