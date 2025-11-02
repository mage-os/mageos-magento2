<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
     * @param array $excludeWebsiteIds
     * @return $this
     */
    public function setExcludeWebsiteIds($excludeWebsiteIds)
    {
        return $this;
    }
}

