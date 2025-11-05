<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Filter\FilterManager;

/**
 * Test helper for FilterManager
 */
class FilterManagerTestHelper extends FilterManager
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * removeTags (custom method for testing)
     *
     * @return mixed
     */
    public function removeTags()
    {
        return $this->data['removeTags'] ?? null;
    }
}
