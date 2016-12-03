<?php


class Swarming_RiseLms_Helper_Quote extends Mage_Core_Helper_Abstract
{

    public function getRelevantProductFromQuoteItem(Mage_Sales_Model_Quote_Item $quoteItem)
    {
        if ($quoteItem->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE) {
            return $quoteItem->getOptionByCode('simple_product')->getProduct();
        }
        else {
            return $quoteItem->getProduct();
        }
    }

    public function isRiseProduct($product, $store = null)
    {

        // Lookup current store if not passed in
        if ($store == null) {
            $store = Mage::app()->getStore();
        }
        // Lookup store from numeric ID
        if (is_numeric($store)) {
            $store = Mage::app()->getStore($store);
        }
        // Get product id
        if ($product instanceof Mage_Catalog_Model_Product) {
            $productId = $product->getId();
        }
        else {
            $productId = $product;
        }
        // Get product resource object
        /** @var Mage_Catalog_Model_Resource_Product $resource */
        $resource = Mage::getModel('catalog/product')->getResource();
        // Lookup whether product is enabled for the website
        $websiteIds = $resource->getWebsiteIds($productId);
        if (in_array($store->getWebsite()->getId(), $websiteIds)) {
            // Product is enabled for website
            // Lookup whether product enabled / disabled as a course
            $riseCourseUuid = $resource->getAttributeRawValue($productId, 'rise_course_uuid', $store);
            $isProductEnabled = !empty($riseCourseUuid);
        }
        else {
            // Product not enabled for website, so by proxy we say not available for RiseLMS Fullfillment
            $isProductEnabled = false;
        }

        return $isProductEnabled;
    }

    /**
     * Does current quote (passed in quote or current shopping cart in session) have any products which are flagged for Course Signup?
     *
     * @param Mage_Sales_Model_Quote $quote Quote to check.  If null, method will check quote from cart session
     * @return bool
     */
    public function hasProductsToCreateNewCourseSignup(Mage_Sales_Model_Quote $quote = null)
    {
        // If passed in quote is empty, get quote from cart in session
        if($quote == null) {
            if (Mage::app()->getStore()->isAdmin()) {
                $quote = Mage::getSingleton("adminhtml/session_quote")->getQuote();
            } else {
                // Get cart, quote and quote item
                /** @var Mage_Checkout_Model_Cart $cart */
                $cart = Mage::getSingleton('checkout/cart');
                // Get quote
                $quote = $cart->getQuote();
            }
        }
        // Iterate items in quote
        /** @var Mage_Sales_Model_Quote_Item $quoteItem */
        foreach ($quote->getAllVisibleItems() as $quoteItem) {
            $product = $this->getRelevantProductFromQuoteItem($quoteItem);
            // Lookup whether product enabled / as a course
            return $this->isRiseProduct($product, $quote->getStore());
        }

        // Didn't find any, return false
        return false;
    }
}