<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\App\Request\Http;

/**
 * Test helper for HTTP Request
 * 
 * Extends Http and adds custom test methods
 */
class HttpTestHelper extends Http
{
    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection
    }

    /**
     * Custom method for getPostValue (does not exist in parent)
     *
     * @param string|null $key
     * @param mixed $defaultValue
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getPostValue($key = null, $defaultValue = null)
    {
        return $defaultValue;
    }

    /**
     * Custom method for has (does not exist in parent)
     *
     * @param string $key
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function has($key): bool
    {
        return false;
    }
}

