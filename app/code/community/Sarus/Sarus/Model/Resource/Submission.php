<?php

class Sarus_Sarus_Model_Resource_Submission extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('sarus_sarus/sarus_submission', 'entity_id');
    }

    /**
     * @param \Sarus_Sarus_Model_Submission|\Mage_Core_Model_Abstract $object
     * @return $this
     */
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ($object->getStatus() && $object->getStatus() != Sarus_Sarus_Model_Submission::STATUS_PENDING) {
            $object->setData('submission_time', new \Zend_Db_Expr('NOW()'));
        }

        return parent::_beforeSave($object);
    }
}
