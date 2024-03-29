<?php

class Sarus_Sarus_Helper_Creditmemo
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
     * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
     * @return array
     */
    public function getSarusProductUuids($creditmemo)
    {
        $uuids = [];

        /** @var Mage_Sales_Model_Order_Creditmemo_Item $item */
        foreach ($creditmemo->getAllItems() as $item) {
            $product = $item->getOrderItem()->getProduct();
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
