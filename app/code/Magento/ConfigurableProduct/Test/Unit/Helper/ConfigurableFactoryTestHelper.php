<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory;

/**
 * Test helper for Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\ConfigurableFactory
 *
 * Extends the concrete ConfigurableFactory class to add custom methods for testing
 */
class ConfigurableFactoryTestHelper extends ConfigurableFactory
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Create method for testing
     *
     * @param array $data
     * @return self
     */
    public function create(array $data = [])
    {
        $this->data['create_data'] = $data;
        return $this;
    }

    /**
     * Save products method for testing
     *
     * @return self
     */
    public function saveProducts()
    {
        $this->data['save_products_called'] = true;
        return $this;
    }
}
