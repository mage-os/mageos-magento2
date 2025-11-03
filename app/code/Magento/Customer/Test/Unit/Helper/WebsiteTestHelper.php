<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Store\Model\Website;

/**
 * Test helper for Website to support custom methods
 */
class WebsiteTestHelper extends Website
{
    /**
     * @var array
     */
    private array $testData = [];

    /**
     * Constructor - skip parent
     *
     * @SuppressWarnings(PHPMD.UselessOverridingMethod)
     */
    public function __construct()
    {
        // Skip parent constructor to avoid ObjectManager dependency
    }

    /**
     * Get website ID
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->testData['website_id'] ?? null;
    }

    /**
     * Set website ID
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId(int $websiteId): self
    {
        $this->testData['website_id'] = $websiteId;
        return $this;
    }
}
