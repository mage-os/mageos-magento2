<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory;

/**
 * Test helper for ResourceFactory
 *
 * This helper extends the concrete ResourceFactory class to provide
 * test-specific functionality without dependency injection issues.
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ResourceFactoryTestHelper extends ResourceModelFactory
{
    /**
     * @var string
     */
    private $tableName = 'tableName';

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues in tests
    }

    /**
     * Get table name
     *
     * @param string $tableName
     * @return string
     */
    public function getTable($tableName)
    {
        return $this->tableName;
    }

    /**
     * Set table name
     *
     * @param string $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * Create method for testing
     *
     * @param array $data
     * @return $this
     */
    public function create(array $data = [])
    {
        return $this;
    }
}