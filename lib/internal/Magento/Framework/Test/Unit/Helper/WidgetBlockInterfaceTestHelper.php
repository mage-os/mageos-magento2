<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Element\BlockInterface;

/**
 * Test helper for Widget BlockInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class WidgetBlockInterfaceTestHelper implements BlockInterface
{
    /**
     * @var string
     */
    private $widgetType = '';

    /**
     * @var array
     */
    private $widgetValues = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $htmlOutput = '';

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set widget type
     *
     * @param string $type
     * @return $this
     */
    public function setWidgetType($type)
    {
        $this->widgetType = $type;
        return $this;
    }

    /**
     * Get widget type
     *
     * @return string
     */
    public function getWidgetType()
    {
        return $this->widgetType;
    }

    /**
     * Set widget values
     *
     * @param array $values
     * @return $this
     */
    public function setWidgetValues($values)
    {
        $this->widgetValues = $values;
        return $this;
    }

    /**
     * Get widget values
     *
     * @return array
     */
    public function getWidgetValues()
    {
        return $this->widgetValues;
    }

    /**
     * Set HTML output
     *
     * @param string $htmlOutput
     * @return $this
     */
    public function setHtmlOutput($htmlOutput)
    {
        $this->htmlOutput = $htmlOutput;
        return $this;
    }

    /**
     * Get HTML output
     *
     * @return string
     */
    public function getHtmlOutput()
    {
        return $this->htmlOutput;
    }

    /**
     * Add data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function addData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Set data
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
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml()
    {
        return $this->htmlOutput;
    }
}
