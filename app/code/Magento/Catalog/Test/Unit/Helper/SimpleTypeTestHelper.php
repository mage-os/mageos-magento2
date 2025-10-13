<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Type\Simple;

/**
 * Test helper for Magento\Catalog\Model\Product\Type\Simple
 *
 * Extends the concrete Simple class to add custom methods for testing
 */
class SimpleTypeTestHelper extends Simple
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
     * Get links for testing
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->data['links'] ?? [];
    }

    /**
     * Set links for testing
     *
     * @param array $links
     * @return self
     */
    public function setLinks($links): self
    {
        $this->data['links'] = $links;
        return $this;
    }
}
