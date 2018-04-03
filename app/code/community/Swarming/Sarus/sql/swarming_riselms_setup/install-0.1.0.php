<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$this->startSetup();

/**
 * Create Resubmission log table and columns
 */
$submissionQueueTable = $installer->getConnection()
    ->newTable($installer->getTable('swarming_riselms/riselms_submission'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned' => true,
        'nullable' => false,
    ), 'Store ID')
    ->addColumn('api_method', Varien_Db_Ddl_Table::TYPE_VARCHAR, 4, array(
        'nullable'  => false,
    ), 'API method used')
    ->addColumn('api_endpoint', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
        'nullable'  => false,
    ), 'API endpoint used')
    ->addColumn('json', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'JSON Payload')
    ->addColumn('counter', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default' => 0
    ), 'Resubmission counter')
    ->addColumn('submission_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT_UPDATE
    ), 'Time of submission to Rise LMS')
    ->addColumn('success', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable'  => false,
        'default' => false
    ), 'Was it successfully submitted?')
    ->addColumn('error_message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Error message')
    ->addIndex(
        $installer->getIdxName($installer->getTable('swarming_riselms/riselms_submission'), array('store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
        array('store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $installer->getIdxName($installer->getTable('swarming_riselms/riselms_submission'), array('counter'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
        array('store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->addIndex(
        $installer->getIdxName($installer->getTable('swarming_riselms/riselms_submission'), array('success'), Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX),
        array('success'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_INDEX)
    )
    ->setComment('Rise LMS resubmission log table');
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

    if (!in_array(Swarming_RiseLms_Model_Product_Type::TYPE_CODE, $applyTo)) {
        $applyTo[] = Swarming_RiseLms_Model_Product_Type::TYPE_CODE;
        $installer->updateAttribute('catalog_product', $attributeCode, 'apply_to', implode(',', $applyTo));
    }
}

$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'rise_course_uuid', array(
    'type' => 'varchar',
    'input' => 'text',
    'label' => 'Rise Course UUID',
    'required' => true,
    'user_defined' => true,
    'note' => 'Unique Identifier for Rise LMS specific courses',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
));



/**
 * Sales quote item and order update
 */
/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

$installer->addAttribute('quote_item', 'rise_course_uuid', array(
    'type' => 'varchar',
    'required' => false,
    'comment' => 'Unique Identifier for Rise LMS specific courses',
    'grid' => false,
));
$installer->addAttribute('order_item', 'rise_course_uuid', array(
    'type' => 'varchar',
    'required' => false,
    'comment' => 'Unique Identifier for Rise LMS specific courses',
    'grid' => false,
));

$this->endSetup();
