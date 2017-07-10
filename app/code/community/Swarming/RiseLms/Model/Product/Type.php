<?php

class Swarming_RiseLms_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'riselms';

    const ATTRIBUTE_SET_NAME = 'Rise LMS';

    const ATTRIBUTE_COURSE_UUID = 'rise_course_uuid';

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        return true;
    }
}
