<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Test helper for Magento\Framework\Data\Form\Element\Fieldset
 */
class FieldsetTestHelper extends Fieldset
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
     * getTooltip (custom method for testing)
     *
     * @return mixed
     */
    public function getTooltip()
    {
        return $this->data['tooltip'] ?? null;
    }

    /**
     * getIsNested (custom method for testing)
     *
     * @return mixed
     */
    public function getIsNested()
    {
        return $this->data['isNested'] ?? null;
    }

    /**
     * getExpanded (custom method for testing)
     *
     * @return mixed
     */
    public function getExpanded()
    {
        return $this->data['expanded'] ?? null;
    }
}
