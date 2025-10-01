<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Link;

/**
 * Test helper for Magento\Bundle\Model\Link
 *
 * Extends the Link class to add custom methods for testing
 */
class LinkTestHelper extends Link
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
        // Skip parent constructor
    }

    /**
     * Custom setSelectionId method for testing
     *
     * @param mixed $selectionId
     * @return self
     */
    public function setSelectionId($selectionId): self
    {
        $this->data['selection_id'] = $selectionId;
        return $this;
    }

    /**
     * Custom getSelectionId method for testing
     *
     * @return mixed
     */
    public function getSelectionId()
    {
        return $this->data['selection_id'] ?? null;
    }

    /**
     * Set test data for flexible state management
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get test data
     *
     * @param string $key
     * @return mixed
     */
    public function getTestData(string $key)
    {
        return $this->data[$key] ?? null;
    }
}
