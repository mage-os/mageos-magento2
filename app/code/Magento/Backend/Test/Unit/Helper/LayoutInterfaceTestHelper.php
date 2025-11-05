<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\View\LayoutInterface;

/**
 * Test helper for LayoutInterface
 */
class LayoutInterfaceTestHelper extends LayoutInterface
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
     * setGeneratorPool (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setGeneratorPool($value)
    {
        $this->data['generatorPool'] = $value;
        return $this;
    }
}
