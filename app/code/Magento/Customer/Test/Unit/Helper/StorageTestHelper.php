<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Session\Storage;

/**
 * Test helper for Storage with custom methods
 */
class StorageTestHelper extends Storage
{
    /**
     * @var array<string, mixed>
     */
    private array $testData = [];

    /**
     * @var bool
     */
    private bool $isCustomerEmulated = false;

    /**
     * Constructor that skips parent to avoid dependency injection
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get is customer emulated
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsCustomerEmulated(): bool
    {
        return $this->isCustomerEmulated;
    }

    /**
     * Set is customer emulated
     *
     * @param bool $value
     * @return void
     */
    public function setIsCustomerEmulated(bool $value): void
    {
        $this->isCustomerEmulated = $value;
    }

    /**
     * Unset is customer emulated
     *
     * @return void
     */
    public function unsIsCustomerEmulated(): void
    {
        $this->isCustomerEmulated = false;
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
}
