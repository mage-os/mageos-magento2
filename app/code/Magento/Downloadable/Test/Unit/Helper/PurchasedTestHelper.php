<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Link\Purchased;

/**
 * Test helper class for Purchased with custom methods
 */
class PurchasedTestHelper extends Purchased
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom setLinkSectionTitle method for testing
     *
     * @param string $title
     * @return self
     */
    public function setLinkSectionTitle($title): self
    {
        return $this;
    }

    /**
     * Override load method
     *
     * @param mixed $id
     * @param mixed $field
     * @return self
     */
    public function load($id, $field = null): self
    {
        return $this;
    }

    /**
     * Override save method
     *
     * @return self
     */
    public function save(): self
    {
        return $this;
    }

    /**
     * Override getId method
     *
     * @return int|null
     */
    public function getId()
    {
        return null;
    }

    /**
     * Get customer ID for testing
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return null;
    }
}
