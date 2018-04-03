<?php

class Swarming_RiseLms_Model_Resource_Submission_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('swarming_riselms/submission');
    }

    /**
     * @param int[] $submissionIds
     * @return $this
     */
    public function filterIds($submissionIds)
    {
        $this->addFilter('entity_id', array('in' => $submissionIds), 'public');
        return $this;
    }

    /**
     * @param int $storeId
     * @return $this
     */
    public function filterStore($storeId)
    {
        $this->addFilter('store_id', $storeId);
        return $this;
    }

    /**
     * @param int $maxTimes
     * @return $this
     */
    public function filterFailed($maxTimes = 0)
    {
        $this->addFilter('success', false);
        if ($maxTimes > 0) {
            $this->addFilter('counter', array('lteq' => $maxTimes), 'public');
        }
        return $this;
    }
}
