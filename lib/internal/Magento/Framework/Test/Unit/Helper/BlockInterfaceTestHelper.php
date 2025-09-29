<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Widget\Block\BlockInterface;

/**
 * Test helper for Widget BlockInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class BlockInterfaceTestHelper implements BlockInterface
{
    /**
     * @var string
     */
    private $returnedResult = '';

    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set returned result
     *
     * @param string $returnedResult
     * @return $this
     */
    public function setReturnedResult($returnedResult)
    {
        $this->returnedResult = $returnedResult;
        return $this;
    }

    /**
     * Get returned result
     *
     * @return string
     */
    public function getReturnedResult()
    {
        return $this->returnedResult;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        
        if (!isset($this->data[$key])) {
            return null;
        }
        
        if ($index !== null) {
            $value = $this->data[$key];
            if (is_array($value) && isset($value[$index])) {
                return $value[$index];
            }
            return null;
        }
        
        return $this->data[$key];
    }

    /**
     * Add data to the widget
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr)
    {
        $this->data = array_merge($this->data, $arr);
        return $this;
    }

    /**
     * Overwrite data in the widget
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->returnedResult;
    }
}
