<?php

class Swarming_RiseLms_Helper_Product extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isRiseLms($product)
    {
        return !empty($product->getData(Swarming_RiseLms_Model_Product_Type::ATTRIBUTE_COURSE_UUID));
    }
}
