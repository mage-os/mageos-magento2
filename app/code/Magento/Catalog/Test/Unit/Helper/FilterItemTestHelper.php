<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Layer\Filter\Item;

/**
 * Mock class for Filter Item with additional methods
 */
class FilterItemTestHelper extends Item
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

    /**
     * Mock method for setFilter
     *
     * @param mixed $filter
     * @return $this
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
        return $this;
    }

    /**
     * Mock method for setLabel
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Mock method for setValue
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Mock method for setCount
     *
     * @param int $count
     * @return $this
     */
    public function setCount($count)
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Required method from Item
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
