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
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QuoteTestHelper extends Quote
{
    /** @var int */
    public int $customerId = 1;
    /** @var array */
    public array $items = [];

    public function __construct()
    {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getItemById($itemId)
    {
        return $this->items[$itemId] ?? false;
    }

    public function removeItem($itemId)
    {
        unset($this->items[$itemId]);
        return $this;
    }

    public function setHasError($flag): self
    {
        return $this;
    }

    public function __wakeup(): void
    {
    }
}
