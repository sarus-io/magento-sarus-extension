<?php
/**
 * Created by PhpStorm.
 * User: matt
 * Date: 12/4/15
 * Time: 1:16 PM
 */ 
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$this->startSetup();

if ($installer->getConnection()->isTableExists($installer->getTable('swarming_riselms/riselms_submissionqueue'))) {
    $installer->getConnection()->dropTable($installer->getTable('swarming_riselms/riselms_submissionqueue'));
}


/**
 * Create Resubmission log table and columns
 */
$submissionQueueTable = $installer->getConnection()
    ->newTable($installer->getTable('swarming_riselms/riselms_submissionqueue'))
    ->addColumn('submissionqueue_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Id')
    ->addColumn('json', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'JSON Payload')
    ->addColumn('counter', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default' => 0
    ), 'Resubmission counter')
    ->addColumn('submission_time', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        'default' => 'DEFAULT_TIMESTAMP'
    ), 'Time of submission to RiseLMS')
    ->addColumn('success', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
        'nullable'  => false,
        'default' => false
    ), 'Was it successfully submitted?')
    ->addColumn('error_message', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'Error message')
    ->addColumn('api_method', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => false,
    ), 'API method used');
$installer->getConnection()->createTable($submissionQueueTable);

/* @var $installer Mage_Eav_Model_Entity_Setup */
$installer = new Mage_Catalog_Model_Resource_Setup('core_setup');

if ($installer->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'rise_course_uuid')) {
    $installer->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'rise_course_uuid');
}


/**
 * Create rise_course_uuid attribute on the product
 */
$installer->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'rise_course_uuid', array(
    'type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
    'input' => 'text',
    'label' => 'Rise Course UUID',
    'required' => 0,
    'user_defined' => 1,
    'note' => 'Unique Identifier for RiseLMS specific courses',
    'global' => 1,
    'visible' => 1,
));

/* @var $installer Mage_Sales_Model_Resource_Setup */
$installer = new Mage_Sales_Model_Resource_Setup('core_setup');

/**
 * Create rise_course_uuid on the sales_flat_quote_item table
 */
//if (!$installer->getAttribute('quote_item', 'rise_course_uuid')) {
    //die();
    $installer->addAttribute('quote_item', 'rise_course_uuid', array(
        'type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
        'required' => 0,
        'comment' => 'Unique Identifier for RiseLMS specific courses',
        'grid' => false,
    ));
//}

/**
 * Create rise_course_uuid on the sales_flat_order_item table
 */
//if (!$installer->getAttribute('order_item', 'rise_course_uuid')) {
    $installer->addAttribute('order_item', 'rise_course_uuid', array(
        'type' => Varien_Db_Ddl_Table::TYPE_VARCHAR,
        'required' => 0,
        'comment' => 'Unique Identifier for RiseLMS specific courses',
        'grid' => false,
    ));
//}

$this->endSetup();