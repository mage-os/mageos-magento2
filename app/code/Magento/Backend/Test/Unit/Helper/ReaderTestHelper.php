<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Config\Reader\Filesystem;

/**
 * Test helper for Reader
 */
class ReaderTestHelper extends Reader
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * getConfiguration (custom method for testing)
     *
     * @return mixed
     */
    public function getConfiguration()
    {
        return $this->data['configuration'] ?? null;
    }
}
