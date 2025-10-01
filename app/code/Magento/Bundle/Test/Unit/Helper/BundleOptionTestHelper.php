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
     * @var array
     */
    private $testData = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Override getData for testing
     *
     * @param string|null $key
     * @param mixed $index
     * @return mixed
     */
        /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null, $index = null)
    {
        if ($key === null) {
            // Return test data when no key specified
            return $this->testData['getData_return'] ?? $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Set test data for getData() method
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->testData[$key] = $value;
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
     * @return self
     */
    public function setOptionId($optionId): self
    {
        $this->data['option_id'] = $optionId;
        return $this;
    }

    /**
     * Get title for testing
     *
     * @return mixed
     */
    public function getTitle()
    {
        return $this->data['title'] ?? null;
    }

    /**
     * Set title for testing
     *
     * @param mixed $title
     * @return self
     */
    public function setTitle($title): self
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * Get default title for testing
     *
     * @return mixed
     */
    public function getDefaultTitle()
    {
        return $this->data['default_title'] ?? null;
    }

    /**
     * Set default title for testing
     *
     * @param mixed $defaultTitle
     * @return self
     */
    public function setDefaultTitle($defaultTitle): self
    {
        $this->data['default_title'] = $defaultTitle;
        return $this;
    }

    /**
     * Get required for testing
     *
     * @return mixed
     */
    public function getRequired()
    {
        return $this->data['required'] ?? false;
    }

    /**
     * Set required for testing
     *
     * @param mixed $required
     * @return self
     */
    public function setRequired($required): self
    {
        $this->data['required'] = $required;
        return $this;
    }

    /**
     * Get selections for testing
     *
     * @return array
     */
    public function getSelections(): array
    {
        return $this->data['selections'] ?? [];
    }

    /**
     * Set selections for testing
     *
     * @param array $selections
     * @return self
     */
    public function setSelections(array $selections): self
    {
        $this->data['selections'] = $selections;
        return $this;
    }

    /**
     * Get type for testing
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->data['type'] ?? null;
    }

    /**
     * Set type for testing
     *
     * @param mixed $type
     * @return self
     */
    public function setType($type): self
    {
        $this->data['type'] = $type;
        return $this;
    }
}
