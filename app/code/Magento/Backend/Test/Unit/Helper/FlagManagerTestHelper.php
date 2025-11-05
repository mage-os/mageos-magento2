<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\FlagManager;

/**
 * Test helper for FlagManager
 */
class FlagManagerTestHelper extends FlagManager
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
     * create (custom method for testing)
     *
     * @return mixed
     */
    public function create()
    {
        return $this->data['create'] ?? null;
    }
}
