<?php
/**
 * Created by PhpStorm.
 * User: emilstewart
 * Date: 7/6/15
 * Time: 1:08 PM
 */ 
class Swarming_RiseLms_Model_Resource_Submissionqueue extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('swarming_riselms/riselms_submissionqueue', 'submissionqueue_id');
    }
}