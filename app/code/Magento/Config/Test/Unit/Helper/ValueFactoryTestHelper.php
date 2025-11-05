<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\App\Config\ValueFactory;

/**
 * Test helper for ValueFactory
 */
class ValueFactoryTestHelper extends ValueFactory
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
     * getCollection (custom method for testing)
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->data['collection'] ?? null;
    }
}
