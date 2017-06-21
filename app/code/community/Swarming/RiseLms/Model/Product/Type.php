<?php

class Swarming_RiseLms_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Virtual
{
    const TYPE_CODE = 'riselms';

    // protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    // {
    //     $product = $this->getProduct($product);
    //     $riselmsPrice = (float)$buyRequest->getRiselmsPrice();
    //     $isStrictProcessMode = $this->_isStrictProcessMode($processMode);

    //     if ($buyRequest->getQty() > 1) {
    //         return Mage::helper('catalog')->__('The maximum qty of riselms product allowed in cart is 1.');
    //     }

    //     if (!$isStrictProcessMode || $riselmsPrice > 0) {
    //         $maxPrice = Mage::helper('riselms_product')->getMaxPrice();
    //         if ($maxPrice && $riselmsPrice > $maxPrice) {
    //             return Mage::helper('catalog')->__('Riselms price must not be greater than %d.', $maxPrice);
    //         }

    //         $products = parent::_prepareProduct($buyRequest, $product, $processMode);
    //         if (!isset($products[0])) {
    //             return Mage::helper('checkout')->__('Cannot process the item.');
    //         }
    //         return $products;
    //     }

    //     return Mage::helper('catalog')->__('Please specify the product price.');
    // }
}
