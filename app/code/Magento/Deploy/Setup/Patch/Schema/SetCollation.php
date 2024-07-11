<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Magento\Deploy\Setup\Patch\Schema;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class SetCollation implements SchemaPatchInterface
{
    /**
     * @var SchemaSetupInterface
     */
    private $schemaSetup;

    /**
     * Constructor.
     *
     * @param SchemaSetupInterface $schemaSetup
     */
    public function __construct(
        SchemaSetupInterface $schemaSetup
    ) {
        $this->schemaSetup = $schemaSetup;
    }

    /**
     * @inheritdoc
     *
     * @return void
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $setup = $this->schemaSetup;

        if ($setup->getConnection()->isTableExists('cache')) {
//            $setup->run("ALTER TABLE `cache` MODIFY COLUMN `id` varchar(200),
//                         DEFAULT CHARSET=utf8mb4, DEFAULT COLLATE=utf8mb4_general_ci");
        }
        if ($setup->getConnection()->isTableExists('cache_tag')) {
//            $setup->run("ALTER TABLE `cache_tag` MODIFY COLUMN `tag` varchar(100),
//                         MODIFY COLUMN `cache_id` varchar(200), DEFAULT CHARSET=utf8mb4,
//                         DEFAULT COLLATE=utf8mb4_general_ci");
        }
        if ($setup->getConnection()->isTableExists('flag')) {
//            $setup->run("ALTER TABLE `flag` MODIFY COLUMN `flag_code` varchar(255),
//                         MODIFY COLUMN `flag_data` mediumtext,DEFAULT CHARSET=utf8mb4,
//                         DEFAULT COLLATE=utf8mb4_general_ci");
        }
        if ($setup->getConnection()->isTableExists('session')) {
//            $setup->run("ALTER TABLE `session` MODIFY COLUMN `session_id` varchar(255),
//                         DEFAULT CHARSET=utf8mb4, DEFAULT COLLATE=utf8mb4_general_ci");
        }
        if ($setup->getConnection()->isTableExists('setup_module')) {
//            $setup->run("ALTER TABLE `setup_module` MODIFY COLUMN `module` varchar(50),
//                         MODIFY COLUMN `schema_version` varchar(50), MODIFY COLUMN `data_version` varchar(50),
//                         DEFAULT CHARSET=utf8mb4, DEFAULT COLLATE=utf8mb4_general_ci");
        }
        if ($setup->getConnection()->isTableExists('design_config_grid_flat')) {
//            $setup->run("ALTER TABLE `design_config_grid_flat` MODIFY COLUMN `theme_theme_id`
//                         varchar(255),DEFAULT CHARSET=utf8mb4, DEFAULT COLLATE=utf8mb4_general_ci");
        }

        //set utf8mb4 for the below tables
        $clTable = [
            'catalog_category_product_cl',
//            'catalog_product_attribute_cl',
//            'catalog_product_category_cl',
//            'catalog_product_price_cl',
//            'cataloginventory_stock_cl',
//            'catalogrule_product_cl',
//            'catalogrule_rule_cl',
//            'catalogsearch_fulltext_cl',
//            'customer_dummy_cl',
//            'design_config_dummy_cl',
//            'salesrule_rule_cl',
//            'targetrule_product_rule_cl',
//            'targetrule_rule_product_cl'
        ];

        foreach ($clTable as $table) {
            if ($setup->getConnection()->isTableExists($table)) {
//                $setup->run("ALTER TABLE $table DEFAULT CHARSET=utf8mb4, DEFAULT COLLATE=utf8mb4_general_ci");
            }
        }

        $this->schemaSetup->endSetup();
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }
}
