<?php

class Swarming_Sarus_Helper_Product extends Mage_Core_Helper_Abstract
{
    /**
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function isSarus($product)
    {
        return !empty($product->getData(Swarming_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID));
    }
}
