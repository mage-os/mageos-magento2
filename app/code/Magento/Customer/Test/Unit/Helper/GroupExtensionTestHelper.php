<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Api\Data\GroupExtension;

/**
 * Test helper for GroupExtension with custom methods
 */
class GroupExtensionTestHelper extends GroupExtension
{
    /**
     * @var array|null
     */
    private $excludeWebsiteIds = null;

    /**
     * Set exclude website IDs (custom method for tests)
     *
     * @param mixed $excludeWebsiteIds
     * @return $this
     */
    public function setExcludeWebsiteIds($excludeWebsiteIds): self
    {
        $this->excludeWebsiteIds = $excludeWebsiteIds;
        return $this;
    }

    /**
     * Get exclude website IDs
     *
     * @return array|null
     */
    public function getExcludeWebsiteIds(): ?array
    {
        return $this->excludeWebsiteIds;
    }
}
