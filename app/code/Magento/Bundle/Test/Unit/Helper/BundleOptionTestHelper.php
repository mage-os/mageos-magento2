<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Option;

/**
 * Test helper for Magento\Bundle\Model\Option
 *
 * Extends Bundle Option to add custom methods for testing
 */
class BundleOptionTestHelper extends Option
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor - clean initialization
        $this->data = [];
    }

    /**
     * Get selections for testing
     *
     * @return array
     */
    public function getSelections()
    {
        return $this->data['selections'] ?? [];
    }

    /**
     * Set selections for testing
     *
     * @param array $selections
     * @return self
     */
    public function setSelections($selections): self
    {
        $this->data['selections'] = $selections;
        return $this;
    }
}
