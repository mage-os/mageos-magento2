<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Configuration\Item\Option;

/**
 * Test helper for Magento\Catalog\Model\Product\Configuration\Item\Option
 * 
 * Extends Option to add custom methods for testing
 */
class ItemOptionTestHelper extends Option
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get value for testing
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->data['value'] ?? null;
    }

    /**
     * Set value for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setValue($value): self
    {
        $this->data['value'] = $value;
        return $this;
    }
}
