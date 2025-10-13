<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Exception;

/**
 * Test helper for testing non-ModifierInterface objects
 * This class intentionally does NOT implement ModifierInterface
 * to test exception scenarios
 */
class ModifierTestHelper extends Exception
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
     * Modify meta for testing
     *
     * @param array $meta
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function modifyMeta($meta)
    {
        return $this->data['meta'] ?? $meta;
    }

    /**
     * Set meta for testing
     *
     * @param array $meta
     * @return self
     */
    public function setMeta($meta): self
    {
        $this->data['meta'] = $meta;
        return $this;
    }
}
