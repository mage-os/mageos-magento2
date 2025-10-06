<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Checkout\Model\Session;

/**
 * Test helper class for CheckoutSession with custom methods
 */
class CheckoutSessionTestHelper extends Session
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom getHasDownloadableProducts method for testing
     *
     * @return bool
     */
    public function getHasDownloadableProducts()
    {
        return false;
    }

    /**
     * Custom setHasDownloadableProducts method for testing
     *
     * @param bool $hasDownloadableProducts
     * @return self
     */
    public function setHasDownloadableProducts($hasDownloadableProducts): self
    {
        return $this;
    }
}
