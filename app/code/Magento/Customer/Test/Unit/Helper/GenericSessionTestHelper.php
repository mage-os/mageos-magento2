<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\Session\Generic as GenericSession;

/**
 * Test helper for GenericSession with custom methods
 */
class GenericSessionTestHelper extends GenericSession
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * Constructor that skips parent to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     * @SuppressWarnings(PHPMD.ElseExpression)
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->testData = $key;
        } else {
            $this->testData[$key] = $value;
        }
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param bool $clear
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function getData($key = '', $clear = false)
    {
        if ($key === '') {
            return $this->testData;
        }
        return $this->testData[$key] ?? null;
    }

    /**
     * Clear storage
     *
     * @return $this
     */
    public function clearStorage()
    {
        $this->testData = [];
        return $this;
    }
}
