<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Menu\Item;

/**
 * Test helper for Item
 */
class ItemTestHelper extends Item
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
     * getFirstAvailable (custom method for testing)
     *
     * @return mixed
     */
    public function getFirstAvailable()
    {
        return $this->data['firstAvailable'] ?? null;
    }
}
