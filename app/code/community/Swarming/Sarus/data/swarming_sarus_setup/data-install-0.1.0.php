<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

/** @var Mage_Catalog_Model_Resource_Product $productResource */
$productResource = Mage::getResourceModel('catalog/product');
$productEntityType = $productResource->getEntityType();

/** @var Mage_Eav_Model_Entity_Attribute_Set $attributeSet */
$attributeSet = Mage::getModel('eav/entity_attribute_set');
$attributeSet->setEntityTypeId($productEntityType->getId());
$attributeSet->setAttributeSetName(Swarming_Sarus_Model_Product_Type::ATTRIBUTE_SET_NAME);
$attributeSet->validate();
$attributeSet->save();
$attributeSet->initFromSkeleton($productEntityType->getDefaultAttributeSetId());
$attributeSet->save();

$riseAttributes = array(
    Swarming_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID
);

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
foreach ($riseAttributes as $attributeCode) {
    $setup->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, Swarming_Sarus_Model_Product_Type::ATTRIBUTE_SET_NAME, 'General', $attributeCode);
}
