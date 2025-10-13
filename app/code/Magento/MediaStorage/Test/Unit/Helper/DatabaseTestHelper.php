<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\MediaStorage\Test\Unit\Helper;

use Magento\MediaStorage\Helper\File\Storage\Database;

/**
 * Test helper for MediaStorage Database
 *
 * Adds custom method for testing file storage database functionality.
 * Follows the migration rule: only add custom methods that don't exist in parent.
 */
class DatabaseTestHelper extends Database
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Custom create method for testing
     *
     *
     * @return mixed
     */
    public function create()
    {
        return null;
    }
}
