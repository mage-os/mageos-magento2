<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Model\Test\Unit\Helper;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Test helper for AbstractDb class
 */
class AbstractDbTestHelper extends AbstractDb
{
    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Protected construct method
     *
     * @return void
     */
    protected function _construct()
    {
        // Skip parent implementation
    }

    /**
     * Delete folder
     *
     * @param string $path
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function deleteFolder($path)
    {
        return true;
    }
}
