<?php

class Sarus_Sarus_Helper_Order
{
    /**
     * @var Sarus_Sarus_Helper_Product
     */
    protected $_productHelper;

    public function __construct()
    {
        $this->_productHelper = Mage::helper('sarus_sarus/product');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getSarusProductIds($order)
    {
        $sarusProductIds = [];

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            if ($product->isComposite()) {
                continue;
            }
            if ($this->_productHelper->isSarus($product)) {
                $sarusProductIds[] = $product->getId();
            }
        }

        return $sarusProductIds;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getSarusProductUuids($order)
    {
        $uuids = [];

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            if ($product->isComposite()) {
                continue;
            }
            if ($this->_productHelper->isSarus($product)) {
                $uuids[] = $product->getData(Sarus_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID);
            }
        }

        return $uuids;
    }
}
