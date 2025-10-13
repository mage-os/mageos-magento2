<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Product\Type;

/**
 * Test helper for Magento\Downloadable\Model\Product\Type
 *
 * Extends the concrete Type class to add custom methods for testing
 */
class TypeTestHelper extends Type
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
     * Set samples for testing
     *
     * @param array $samples
     * @return self
     */
    public function setSamples($samples): self
    {
        $this->data['samples'] = $samples;
        return $this;
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
