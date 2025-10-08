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

    /**
     * Get title for testing
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->data['title'] ?? null;
    }

    /**
     * Set title for testing
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title): self
    {
        $this->data['title'] = $title;
        return $this;
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