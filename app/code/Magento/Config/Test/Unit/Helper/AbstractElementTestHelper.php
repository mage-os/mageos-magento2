<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Test helper for AbstractElement
 */
class AbstractElementTestHelper extends AbstractElement
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
     * getLabel (custom method for testing)
     *
     * @return mixed
     */
    public function getLabel()
    {
        return $this->data['label'] ?? null;
    }
}
