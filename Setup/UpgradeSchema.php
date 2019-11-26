<?php

namespace Magenest\MultipleWishlist\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Magenest\MultipleWishlist\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup,
                            ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.2.1') < 0) {
           $this->createReportWishListTable($installer);
        }

        if (version_compare($context->getVersion(), '1.2.4') < 0) {
           $this->addColumnNoficationToWishListTable($installer);
        }

        if (version_compare($context->getVersion(), '1.2.6') < 0) {
            $this->addColumnCheckSendReminder($installer);
        }

        if (version_compare($context->getVersion(), '1.2.7') < 0) {
           $this->changeColumnCheckSendReminder($installer);
        }

        if (version_compare($context->getVersion(), '1.3.0') < 0) {
           $this->addForeignKey($installer);
        }

        if (version_compare($context->getVersion(), '1.3.1') < 0) {
            $this->changeColumnAddAt($installer);
        }
            $installer->endSetup();
    }

    public function createReportWishListTable(SchemaSetupInterface $installer)
    {
        //Install new database table
        $table = $installer->getConnection()->newTable(
            $installer->getTable('magenest_reportwishlist')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null, [
            'identity' => true,
            'unsigned' => true,
            'nullable' => false,
            'primary' => true
        ],
            'Entity Id'
        )->addColumn(
            'last_added',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null, [
            'nullable' => true,
            'default' =>
                \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT
        ],
            'Last Added'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            64,
            ['nullable' => true],
            'Product Id'
        )->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            ['nullable' => true],
            'Name Product'
        )->addColumn(
            'sku',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            ['nullable' => true],
            'SKU'
        )->addColumn(
            'typeProduct',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            64,
            ['nullable' => true],
            'Product Type'
        )->addColumn(
            'stockQty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            ['nullable' => true],
            'Stock Qty'
        )->addColumn(
            'count',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            ['nullable' => true, 'default' => '1'],
            'Count'
        )->addColumn(
            'addOrder',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            255,
            ['nullable' => true],
            'Add Order'
        );
        $installer->getConnection()->createTable($table);
    }

    public function addColumnNoficationToWishListTable(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable( 'wishlist'),
            'notification',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'comment' => 'notification'
            ]
        );
    }

    public function addColumnCheckSendReminder(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->addColumn(
            $installer->getTable( 'wishlist_item'),
            'check_send_reminder',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'comment' => 'check_send_reminder'
            ]
        );
        $installer->getConnection()->addColumn(
            $installer->getTable( 'magenest_multiplewishlist_item'),
            'check_send_reminder',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'comment' => 'check_send_reminder'
            ]
        );
    }

    public function changeColumnCheckSendReminder(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->changeColumn(
            $installer->getTable( 'wishlist_item'),
            'check_send_reminder',
            'check_send_reminder',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'comment' => 'check_send_reminder'
            ]
        );
        $installer->getConnection()->changeColumn(
            $installer->getTable( 'magenest_multiplewishlist_item'),
            'check_send_reminder',
            'check_send_reminder',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                'comment' => 'check_send_reminder'
            ]
        );
    }

    public function addForeignKey(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('magenest_multiplewishlist_item', 'wishlist_id', 'magenest_multiplewishlist_wishlist', 'id'),
            'magenest_multiplewishlist_item',
            'wishlist_id',
            'magenest_multiplewishlist_wishlist',
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('magenest_multiplewishlist_item', 'product_id', 'catalog_product_entity', 'entity_id'),
            'magenest_multiplewishlist_item',
            'product_id',
            'catalog_product_entity',
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('magenest_multiplewishlist_wishlist', 'customer_id', 'customer_entity', 'entity_id'),
            'magenest_multiplewishlist_wishlist',
            'customer_id',
            'customer_entity',
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('magenest_multiplewishlist_item_option', 'multiplewishlist_item_id', 'magenest_multiplewishlist_item', 'id'),
            'magenest_multiplewishlist_item_option',
            'multiplewishlist_item_id',
            'magenest_multiplewishlist_item',
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->addForeignKey(
            $installer->getFkName('magenest_multiplewishlist_item_option', 'product_id', 'catalog_product_entity', 'entity_id'),
            'magenest_multiplewishlist_item_option',
            'product_id',
            'catalog_product_entity',
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
    }

    public function changeColumnAddAt(SchemaSetupInterface $installer)
    {
        $installer->getConnection()->changeColumn(
            $installer->getTable( 'magenest_multiplewishlist_item'),
            'added_at',
            'added_at',
            [
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'type'=>\Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP
            ]
        );
    }
}