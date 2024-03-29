<?php

class Sarus_Sarus_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'sarus';

    const ATTRIBUTE_SET_NAME = 'Sarus';

    const ATTRIBUTE_COURSE_UUID = 'sarus_course_uuid';

    /**
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isVirtual($product = null)
    {
        return true;
    }
}
