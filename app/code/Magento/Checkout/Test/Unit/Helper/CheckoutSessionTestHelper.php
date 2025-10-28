<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Helper;

use Magento\Checkout\Model\Session;

/**
 * Test helper for Checkout Session
 *
 * Adds custom methods for testing downloadable products functionality.
 * Follows the migration rule: only add custom methods that don't exist in parent.
 */
class CheckoutSessionTestHelper extends Session
{
    /**
     * @var bool
     */
    private $hasDownloadableProducts = false;

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get has downloadable products flag
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHasDownloadableProducts(): bool
    {
        return $this->hasDownloadableProducts;
    }

    /**
     * Set has downloadable products flag
     *
     * @param bool $hasDownloadableProducts
     * @return self
     */
    public function setHasDownloadableProducts($hasDownloadableProducts): self
    {
        $this->hasDownloadableProducts = $hasDownloadableProducts;
        return $this;
    }
}
