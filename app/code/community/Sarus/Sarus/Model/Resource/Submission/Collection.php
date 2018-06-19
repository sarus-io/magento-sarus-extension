<?php

class Sarus_Sarus_Model_Resource_Submission_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('sarus_sarus/submission');
    }

    /**
     * @param int[] $submissionIds
     * @return $this
     */
    public function filterIds($submissionIds)
    {
        $this->addFilter('entity_id', ['in' => $submissionIds], 'public');
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
     * @param string $status
     * @return $this
     */
    public function filterStatus($status)
    {
        $this->addFieldToFilter('status', ['eq' => $status]);
        return $this;
    }

    /**
     * @param int $threshold
     * @return $this
     */
    public function filterCounter($threshold)
    {
        $this->addFieldToFilter('counter', ['lt' => $threshold]);
        return $this;
    }
}
