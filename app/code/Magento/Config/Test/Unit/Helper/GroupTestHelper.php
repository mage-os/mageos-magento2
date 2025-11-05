<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Config\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\Fieldset\Group;

/**
 * Test helper for Group
 */
class GroupTestHelper extends Group
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
     * getExpanded (custom method for testing)
     *
     * @return mixed
     */
    public function getExpanded()
    {
        return $this->data['expanded'] ?? null;
    }

    /**
     * getLegend (custom method for testing)
     *
     * @return mixed
     */
    public function getLegend()
    {
        return $this->data['legend'] ?? null;
    }

    /**
     * getComment (custom method for testing)
     *
     * @return mixed
     */
    public function getComment()
    {
        return $this->data['comment'] ?? null;
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
}
