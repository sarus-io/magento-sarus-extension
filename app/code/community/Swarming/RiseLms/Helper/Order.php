<?php

class Swarming_RiseLms_Helper_Order
{
    /**
     * @var Swarming_RiseLms_Helper_Product
     */
    protected $_productHelper;

    public function __construct()
    {
        $this->_productHelper = Mage::helper('swarming_riselms/product');
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getRiseLmsProductIds($order)
    {
        $riseLmsProductIds = [];

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            if ($product->isComposite()) {
                continue;
            }
            if ($this->_productHelper->isRiseLms($product)) {
                $riseLmsProductIds[] = $product->getId();
            }
        }

        return $riseLmsProductIds;
    }

    /**
     * @param Mage_Sales_Model_Order $order
     * @return array
     */
    public function getRiseLmsProductUuids($order)
    {
        $uuids = [];

        /** @var Mage_Sales_Model_Order_Item $item */
        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            if ($product->isComposite()) {
                continue;
            }
            if ($this->_productHelper->isRiseLms($product)) {
                $uuids[] = $product->getData(Swarming_RiseLms_Model_Product_Type::ATTRIBUTE_COURSE_UUID);
            }
        }

        return $uuids;
    }
}
