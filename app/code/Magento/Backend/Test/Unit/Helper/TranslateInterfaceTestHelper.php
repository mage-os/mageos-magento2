<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\TranslateInterface;

/**
 * Test helper for TranslateInterface
 */
class TranslateInterfaceTestHelper extends TranslateInterface
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
     * init (custom method for testing)
     *
     * @return mixed
     */
    public function init()
    {
        return $this->data['init'] ?? null;
    }
}
