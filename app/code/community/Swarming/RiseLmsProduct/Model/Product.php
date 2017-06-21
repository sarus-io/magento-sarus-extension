<?php

class Swarming_RiseLmsProduct_Model_Product extends Mage_Catalog_Model_Product
{
    public function isRiseLmsProduct()
    {
        return $this->getTypeId() == Swarming_RiseLmsProduct_Model_Product_Type::TYPE_CODE;
    }
}