<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Test helper class for Quote used across Quote and related module tests
 */
class QuoteTestHelper extends Quote
{
    /**
     * @var int
     */
    public int $customerId = 1;

    /**
     * @var array
     */
    public array $items = [];

    /**
     * @var bool
     */
    private bool $hasError = false;

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get customer ID
     *
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * Set customer ID
     *
     * @param int $customerId
     * @return $this
     */
    public function setCustomerId($customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    /**
     * Get item by ID
     *
     * @param mixed $itemId
     * @return mixed
     */
    public function getItemById($itemId)
    {
        return $this->items[$itemId] ?? null;
    }

    /**
     * Set item by ID
     *
     * @param mixed $itemId
     * @param mixed $item
     * @return $this
     */
    public function setItemById($itemId, $item): self
    {
        $this->items[$itemId] = $item;
        return $this;
    }

    /**
     * Remove item
     *
     * @param mixed $itemId
     * @return $this
     */
    public function removeItem($itemId): self
    {
        unset($this->items[$itemId]);
        return $this;
    }

    /**
     * Wakeup method
     *
     * @return void
     */
    public function __wakeup(): void
    {
        // No-op for testing
    }

    /**
     * Get has error
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->hasError;
    }

    /**
     * Set has error
     *
     * @param mixed $hasError
     * @return $this
     */
    public function setHasError($hasError): self
    {
        $this->hasError = (bool)$hasError;
        return $this;
    }

    /**
     * Collect totals
     *
     * @return $this
     */
    public function collectTotals(): self
    {
        return $this;
    }
}
