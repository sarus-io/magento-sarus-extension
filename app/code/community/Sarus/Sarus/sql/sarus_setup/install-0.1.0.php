<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$this->startSetup();

/**
 * Create Resubmission log table and columns
 */
$submissionQueueTable = $installer->getConnection()
    ->newTable($installer->getTable('sarus_sarus/sarus_submission'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ], 'Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, [
        'unsigned' => true,
        'nullable' => false,
    ], 'Store ID')
    ->addColumn('request', Varien_Db_Ddl_Table::TYPE_TEXT, null, [
        'nullable'  => false,
    ], 'Serialized Request')
    ->addColumn('counter', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default' => 0
    ), 'Counter')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
        'nullable'  => false,
        'default' => Sarus_Sarus_Model_Submission::STATUS_PENDING
    ), 'Status')
    ->addColumn('error_message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Error message')
    ->addColumn('creating_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT
    ), 'Creating Time')
    ->addColumn('submission_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => true
    ), 'Submission Time')
    ->addForeignKey(
        $installer->getFkName('sarus_sarus/sarus_submission', 'store_id', 'core_store', 'store_id'),
        'store_id',
        $installer->getTable('core_store'),
        'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE
    )
    ->setComment('Sarus Submission Queue');
$installer->getConnection()->createTable($submissionQueueTable);



/**
 * Catalog product update
 */
/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');

$attributes = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'minimal_price',
    'tax_class_id'
);
foreach ($attributes as $attributeCode) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $attributeCode, 'apply_to'));

    if (!in_array(Sarus_Sarus_Model_Product_Type::TYPE_CODE, $applyTo)) {
        $applyTo[] = Sarus_Sarus_Model_Product_Type::TYPE_CODE;
        $installer->updateAttribute('catalog_product', $attributeCode, 'apply_to', implode(',', $applyTo));
    }
}

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, Sarus_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID, array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Sarus Course UUID',
    'required' => true,
    'user_defined' => true,
    'note' => 'Unique Identifier for Sarus specific courses',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
));



/**
 * Sales quote item and order update
 */
/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->addAttribute('quote_item', Sarus_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID, array(
    'type' => 'varchar',
    'required' => false,
    'comment' => 'Unique Identifier for Sarus specific courses',
    'grid' => false,
));
$installer->addAttribute('order_item', Sarus_Sarus_Model_Product_Type::ATTRIBUTE_COURSE_UUID, array(
    'type' => 'varchar',
    'required' => false,
    'comment' => 'Unique Identifier for Sarus specific courses',
    'grid' => false,
));

$this->endSetup();
