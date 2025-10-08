<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Product\Type;

/**
 * Test helper for Downloadable Product Type
 */
class TypeTestHelper extends Type
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
     * Custom __wakeup method for testing
     */
    public function __wakeup()
    {
        // Custom method for testing
    }

    /**
     * Get links
     *
     * @param mixed $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getLinks($product = null)
    {
        return $this->data['links'] ?? [];
    }

    /**
     * Set links
     *
     * @param array $links
     * @return $this
     */
    public function setLinks($links)
    {
        $this->data['links'] = $links;
        return $this;
    }

    /**
     * Get samples
     *
     * @param mixed $product
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getSamples($product = null)
    {
        return $this->data['samples'] ?? [];
    }

    /**
     * Set samples
     *
     * @param array $samples
     * @return $this
     */
    public function setSamples($samples)
    {
        $this->data['samples'] = $samples;
        return $this;
    }
}
