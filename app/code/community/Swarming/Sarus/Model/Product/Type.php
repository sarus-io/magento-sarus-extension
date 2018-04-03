<?php

class Swarming_Sarus_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'sarus';

    const ATTRIBUTE_SET_NAME = 'Sarus';

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
