<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Mock;

use Magento\Framework\Filter\FilterManager;

/**
 * Mock class for FilterManager with additional methods
 */
class FilterManagerMock extends FilterManager
{
    /**
     * Mock method for stripTags
     *
     * @param string $value
     * @return string
     */
    public function stripTags(string $value): string
    {
        return strip_tags($value);
    }
}
