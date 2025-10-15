<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\ResourceModel\Quote\Collection;

/**
 * Test helper for Quote resource collection to expose setQuote() for PHPUnit 12 mocks.
 */
class QuoteCollectionTestHelper extends Collection
{
    /**
     * Set quote reference for tests.
     *
     * @param mixed $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->setData('quote', $quote);
        return $this;
    }
}
