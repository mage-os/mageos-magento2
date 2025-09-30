<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Category;

/**
 * TestHelper for Category with dynamic methods
 */
class CategoryTestHelper extends Category
{
    /** @var array */
    private $changedProductIds = [];

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getChangedProductIds()
    {
        return $this->changedProductIds;
    }

    public function setChangedProductIds($value)
    {
        $this->changedProductIds = $value;
        return $this;
    }

    public function __wakeUp()
    {
        // Implementation for __wakeUp method
    }
}
