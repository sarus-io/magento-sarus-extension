<?php

class Swarming_RiseLms_Helper_Creditmemo
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
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return array
     */
    public function getRiseLmsProductIds($creditmemo)
    {
        $riseLmsProductIds = [];

        /** @var Mage_Sales_Model_Order_Creditmemo_Item $item */
        foreach ($creditmemo->getAllItems() as $item) {
            $product = $item->getOrderItem()->getProduct();
            if ($this->_productHelper->isRiseLms($product)) {
                $riseLmsProductIds[] = $product->getId();
            }
        }

        return $riseLmsProductIds;
    }

    /**
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return array
     */
    public function getRiseLmsProductUuids($creditmemo)
    {
        $uuids = [];

        /** @var Mage_Sales_Model_Order_Creditmemo_Item $item */
        foreach ($creditmemo->getAllItems() as $item) {
            $product = $item->getOrderItem()->getProduct();
            if ($this->_productHelper->isRiseLms($product)) {
                $uuids[] = $product->getData(Swarming_RiseLms_Model_Product_Type::ATTRIBUTE_COURSE_UUID);
            }
        }

        return $uuids;
    }
}
