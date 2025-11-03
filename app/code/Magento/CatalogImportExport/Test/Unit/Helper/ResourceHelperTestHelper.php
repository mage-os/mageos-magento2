<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\ImportExport\Model\ResourceModel\Helper;

/**
 * Test helper for ResourceHelper - extends concrete implementation
 */
class ResourceHelperTestHelper extends Helper
{
    /**
     * @var int
     */
    private $nextAutoincrementValue = 2;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Override to return test value without database dependency
     * 
     * @param string|null $tableName
     * @return int
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getNextAutoincrement($tableName = null)
    {
        return $this->nextAutoincrementValue;
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setNextAutoincrement($value)
    {
        $this->nextAutoincrementValue = $value;
        return $this;
    }
}

