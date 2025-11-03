<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Layer\Filter\Item;

class ItemTestHelper extends Item
{
    /**
     * @var mixed
     */
    private $filter = null;

    /**
     * @var mixed
     */
    private $label = null;

    /**
     * @var mixed
     */
    private $value = null;

    /**
     * @var mixed
     */
    private $count = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * @param mixed $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param mixed $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }
}

