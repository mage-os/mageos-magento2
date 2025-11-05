<?php
/**
 * Copyright © 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Grid\Column;

/**
 * Test helper for Column class with magic methods
 */
class ColumnTestHelper extends Column
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
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get values (magic method for testing)
     *
     * @return mixed
     */
    public function getValues()
    {
        return $this->data['values'] ?? null;
    }

    /**
     * Set values (for test setup)
     *
     * @param mixed $values
     * @return $this
     */
    public function setValues($values)
    {
        $this->data['values'] = $values;
        return $this;
    }

    /**
     * Get index (magic method for testing)
     *
     * @return mixed
     */
    public function getIndex()
    {
        return $this->data['index'] ?? null;
    }

    /**
     * Set index (for test setup)
     *
     * @param mixed $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->data['index'] = $index;
        return $this;
    }

    /**
     * Get separator (magic method for testing)
     *
     * @return mixed
     */
    public function getSeparator()
    {
        return $this->data['separator'] ?? null;
    }

    /**
     * Set separator (for test setup)
     *
     * @param mixed $separator
     * @return $this
     */
    public function setSeparator($separator)
    {
        $this->data['separator'] = $separator;
        return $this;
    }

    /**
     * Get getter (magic method for testing)
     *
     * @return mixed
     */
    public function getGetter()
    {
        return $this->data['getter'] ?? null;
    }

    /**
     * Set getter (for test setup)
     *
     * @param mixed $getter
     * @return $this
     */
    public function setGetter($getter)
    {
        $this->data['getter'] = $getter;
        return $this;
    }
}
