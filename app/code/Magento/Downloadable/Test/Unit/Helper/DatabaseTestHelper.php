<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\MediaStorage\Helper\File\Storage\Database;

/**
 * Test helper class for Database with custom methods
 */
class DatabaseTestHelper extends Database
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom create method for testing
     *
     * @return mixed
     */
    public function create()
    {
        return null;
    }
}
