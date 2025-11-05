<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\DB\TransactionFactory;

/**
 * Test helper for TransactionFactory
 */
class TransactionFactoryTestHelper extends TransactionFactory
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
     * addObject (custom method for testing)
     *
     * @return mixed
     */
    public function addObject()
    {
        return $this->data['addObject'] ?? null;
    }
}
