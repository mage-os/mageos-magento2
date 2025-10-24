<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Api\Data\CartExtension;
use Magento\NegotiableQuote\Api\Data\NegotiableQuoteInterface;

/**
 * Test helper for CartExtension
 *
 * This helper extends the concrete CartExtension class to provide
 * test-specific functionality without dependency injection issues.
 */
class CartExtensionTestHelper extends CartExtension
{
    /**
     * @var NegotiableQuoteInterface
     */
    private $negotiableQuote;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get negotiable quote
     *
     * @return NegotiableQuoteInterface|null
     */
    public function getNegotiableQuote()
    {
        return $this->negotiableQuote;
    }

    /**
     * Set negotiable quote
     *
     * @param NegotiableQuoteInterface $negotiableQuote
     * @return $this
     */
    public function setNegotiableQuote($negotiableQuote)
    {
        $this->negotiableQuote = $negotiableQuote;
        return $this;
    }
}
