<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\CustomerFactory;

/**
 * Test helper for CustomerFactory with custom methods
 */
class CustomerFactoryTestHelper extends CustomerFactory
{
    /**
     * Constructor that skips parent to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Create customer instance
     *
     * @param array $data
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function create(array $data = [])
    {
        // Mock implementation - will be stubbed in tests
        return null;
    }

    /**
     * Save customer
     *
     * @return mixed
     */
    public function save()
    {
        // Mock implementation - will be stubbed in tests
        return null;
    }
}
