<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Menu;

/**
 * Test helper for Menu
 */
class MenuTestHelper extends Menu
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
     * addChild (custom method for testing)
     *
     * @return mixed
     */
    public function addChild()
    {
        return $this->data['addChild'] ?? null;
    }

    /**
     * getFirstAvailableChild (custom method for testing)
     *
     * @return mixed
     */
    public function getFirstAvailableChild()
    {
        return $this->data['firstAvailableChild'] ?? null;
    }
}
