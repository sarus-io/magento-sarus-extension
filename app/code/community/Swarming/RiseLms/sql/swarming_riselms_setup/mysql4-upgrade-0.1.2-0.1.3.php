<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$this->startSetup();

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');

if ($installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'rise_redirect_id')) {
    $installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'rise_redirect_id');
}

/**
 * Create rise_redirect_id attribute on the product
 */
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'rise_redirect_id', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Rise Redirect Product ID',
    'required' => 0,
    'user_defined' => 1,
    'note' => 'Sets product ID of new course to direct customers to, archives current course by preventing it from being aded to the court or linked to RiseLMS.',
    'global' => 1
));

$this->endSetup();