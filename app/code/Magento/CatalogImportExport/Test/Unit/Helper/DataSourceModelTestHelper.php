<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogImportExport\Test\Unit\Helper;

use Magento\ImportExport\Model\ResourceModel\Import\Data;

/**
 * Test helper for Import Data - extends concrete implementation
 */
class DataSourceModelTestHelper extends Data
{
    /**
     * @var mixed
     */
    private $nextUniqueBunchData = null;

    /**
     * @var int
     */
    private $callCount = 0;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Override to return test data without database dependency
     * 
     * @param array|null $ids
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getNextUniqueBunch($ids = null)
    {
        $this->callCount++;
        if ($this->callCount === 1) {
            return $this->nextUniqueBunchData;
        } else {
            return null;
        }
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function setNextUniqueBunchData($data)
    {
        $this->nextUniqueBunchData = $data;
        return $this;
    }
}

