<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
namespace Magento\A\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Uninstall implements \Magento\Framework\Setup\UninstallInterface
{

    /**
     * Uninstall method
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    }
}
