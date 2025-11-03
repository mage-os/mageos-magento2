<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\GroupExtensionInterface;

/**
 * Test helper for GroupExtensionInterface to support custom methods
 */
class GroupExtensionInterfaceTestHelper implements GroupExtensionInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Constructor - skip parent
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Get excluded website IDs
     *
     * @return array|null
     */
    public function getExcludeWebsiteIds()
    {
        return $this->data['exclude_website_ids'] ?? null;
    }

    /**
     * Set excluded website IDs
     *
     * @param mixed $excludeWebsiteIds
     * @return $this
     */
    public function setExcludeWebsiteIds($excludeWebsiteIds)
    {
        $this->data['exclude_website_ids'] = $excludeWebsiteIds;
        return $this;
    }
}
