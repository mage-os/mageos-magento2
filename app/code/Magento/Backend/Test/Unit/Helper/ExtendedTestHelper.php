<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Grid\Column\Extended;

/**
 * Test helper for Extended (Grid Column)
 *
 * This helper extends the concrete Extended class to provide
 * test-specific functionality without dependency injection issues.
 */
class ExtendedTestHelper extends Extended
{
    /**
     * @var array
     */
    private $values = [];

    /**
     * @var string
     */
    private $index = 'result_data';

    /**
     * @var string
     */
    private $htmlName = 'test';

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get values
     *
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set values
     *
     * @param array $values
     * @return $this
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Get index
     *
     * @return string
     */
    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Set index
     *
     * @param string $index
     * @return $this
     */
    public function setIndex($index)
    {
        $this->index = $index;
        return $this;
    }

    /**
     * Get HTML name
     *
     * @return string
     */
    public function getHtmlName()
    {
        return $this->htmlName;
    }

    /**
     * Set HTML name
     *
     * @param string $name
     * @return $this
     */
    public function setHtmlName($name)
    {
        $this->htmlName = $name;
        return $this;
    }
}

