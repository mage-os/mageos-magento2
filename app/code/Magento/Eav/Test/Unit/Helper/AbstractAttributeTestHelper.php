<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;

/**
 * Test helper for AbstractAttribute with custom methods
 */
class AbstractAttributeTestHelper extends AbstractAttribute
{
    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get is visible (custom method for tests)
     *
     * @return bool|null
     */
    public function getIsVisible(): ?bool
    {
        return null;
    }

    /**
     * Get used in forms (custom method for tests)
     *
     * @return array|null
     */
    public function getUsedInForms(): ?array
    {
        return null;
    }
}

