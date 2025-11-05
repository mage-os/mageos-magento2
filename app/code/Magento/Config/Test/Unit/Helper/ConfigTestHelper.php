<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\App\Config;

/**
 * Test helper for Config
 */
class ConfigTestHelper extends Config
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
     * getPrefixes (custom method for testing)
     *
     * @return mixed
     */
    public function getPrefixes()
    {
        return $this->data['prefixes'] ?? null;
    }
}
