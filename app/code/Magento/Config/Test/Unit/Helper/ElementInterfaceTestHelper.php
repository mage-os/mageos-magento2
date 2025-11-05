<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\ElementInterface;

/**
 * Test helper for ElementInterface
 */
class ElementInterfaceTestHelper extends ElementInterface
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
     * current (custom method for testing)
     *
     * @return mixed
     */
    public function current()
    {
        return $this->data['current'] ?? null;
    }

    /**
     * rewind (custom method for testing)
     *
     * @return mixed
     */
    public function rewind()
    {
        return $this->data['rewind'] ?? null;
    }
}
