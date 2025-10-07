<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Option;

/**
 * Test helper for Magento\Catalog\Model\Product\Option
 */
class ProductOptionTestHelper extends Option
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
     * Set product for testing
     *
     * @param \Magento\Catalog\Model\Product|null $product
     * @return $this
     */
    public function setProduct(?\Magento\Catalog\Model\Product $product = null)
    {
        $this->data['product'] = $product;
        return $this;
    }

    /**
     * Get product for testing
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * Save options for testing
     *
     * @return $this
     */
    public function saveOptions()
    {
        // Mock implementation - just return self
        return $this;
    }

    /**
     * Get option ID for testing
     *
     * @return mixed
     */
    public function getOptionId()
    {
        return $this->data['option_id'] ?? null;
    }

    /**
     * Set option ID for testing
     *
     * @param mixed $optionId
     * @return $this
     */
    public function setOptionId($optionId): self
    {
        $this->data['option_id'] = $optionId;
        return $this;
    }
}
