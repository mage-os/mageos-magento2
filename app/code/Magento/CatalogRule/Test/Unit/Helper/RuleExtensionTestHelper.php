<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Helper;

use Magento\CatalogRule\Api\Data\RuleExtension;

/**
 * Test helper for RuleExtension with custom methods
 */
class RuleExtensionTestHelper extends RuleExtension
{
    /**
     * Set exclude website IDs (custom method for tests)
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param array $excludeWebsiteIds
     * @return $this
     */
    public function setExcludeWebsiteIds($excludeWebsiteIds)
    {
        return $this;
    }
}
