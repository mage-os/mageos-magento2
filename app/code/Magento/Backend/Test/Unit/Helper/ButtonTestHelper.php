<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Block\Widget\Button;

/**
 * Test helper for Button class with custom methods
 */
class ButtonTestHelper extends Button
{
    /**
     * @var array
     */
    private $expectations = [];
    
    /**
     * @var array
     */
    private $returnValues = [];
    
    /**
     * @var string|null
     */
    private $currentMethod;

    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Mock expects() method
     *
     * @param mixed $matcher
     * @return $this
     */
    public function expects($matcher)
    {
        return $this;
    }

    /**
     * Mock method() method
     *
     * @param string $method
     * @return $this
     */
    public function method($method)
    {
        $this->currentMethod = $method;
        return $this;
    }

    /**
     * Mock with() method
     *
     * @param mixed ...$args
     * @return $this
     */
    public function with(...$args)
    {
        return $this;
    }

    /**
     * Mock willReturn() method
     *
     * @param mixed $value
     * @return $this
     */
    public function willReturn($value)
    {
        $this->returnValues[$this->currentMethod] = $value;
        return $this;
    }

    /**
     * Mock willReturnSelf() method
     *
     * @return $this
     */
    public function willReturnSelf()
    {
        $this->returnValues[$this->currentMethod] = $this;
        return $this;
    }

    /**
     * Custom isAllowed method for testing
     *
     * @param string|null $resource
     * @return bool
     */
    public function isAllowed($resource = null): bool
    {
        return $this->returnValues['isAllowed'] ?? true;
    }

    /**
     * Override getAuthorization method
     *
     * @return mixed
     */
    public function getAuthorization()
    {
        return $this->returnValues['getAuthorization'] ?? $this;
    }

    /**
     * Override toHtml method
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->returnValues['toHtml'] ?? '';
    }
}
