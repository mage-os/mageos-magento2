<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Test helper for Quote model with custom methods
 */
class QuoteTestHelper extends Quote
{
    /**
     * @var mixed
     */
    private $website = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set website (custom method for tests)
     *
     * @param mixed $website
     * @return $this
     */
    public function setWebsite($website): self
    {
        $this->website = $website;
        return $this;
    }

    /**
     * Get website
     *
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }
}

