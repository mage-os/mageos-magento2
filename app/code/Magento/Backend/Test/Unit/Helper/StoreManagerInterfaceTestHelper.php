<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Store\Model\StoreManagerInterface;

/**
 * Test helper for StoreManagerInterface
 */
class StoreManagerInterfaceTestHelper extends StoreManagerInterface
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
     * something (custom method for testing)
     *
     * @return mixed
     */
    public function something()
    {
        return $this->data['something'] ?? null;
    }
}
