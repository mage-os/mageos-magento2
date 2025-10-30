<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

/**
 * Test helper for AbstractElement with custom methods
 */
class AbstractElementTestHelper extends AbstractElement
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Constructor that skips parent to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get value
     *
     * @param string|null $index
     * @return mixed
     */
    public function getValue($index = null)
    {
        if ($index !== null) {
            return $this->testData['values'][$index] ?? null;
        }
        return $this->testData['value'] ?? null;
    }

    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->testData['value'] = $value;
        return $this;
    }

    /**
     * Serialize element
     *
     * @param array $attributes
     * @param string $valueSeparator
     * @param string $fieldSeparator
     * @param string $quote
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function serialize($attributes = [], $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"'): string
    {
        return '';
    }
}
