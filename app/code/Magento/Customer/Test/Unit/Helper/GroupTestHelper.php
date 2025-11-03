<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Model\Group;

/**
 * Test helper for Group with custom methods
 */
class GroupTestHelper extends Group
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
     * Get tax class ID
     *
     * @return int|null
     */
    public function getTaxClassId()
    {
        return $this->testData['tax_class_id'] ?? null;
    }

    /**
     * Set tax class ID
     *
     * @param int $taxClassId
     * @return $this
     */
    public function setTaxClassId(int $taxClassId): self
    {
        $this->testData['tax_class_id'] = $taxClassId;
        return $this;
    }

    /**
     * Get tax class name
     *
     * @return string|null
     */
    public function getTaxClassName()
    {
        return $this->testData['tax_class_name'] ?? null;
    }

    /**
     * Set tax class name
     *
     * @param string $taxClassName
     * @return $this
     */
    public function setTaxClassName(string $taxClassName): self
    {
        $this->testData['tax_class_name'] = $taxClassName;
        return $this;
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->testData['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->testData['id'] = $id;
        return $this;
    }

    /**
     * Get code
     *
     * @return string|null
     */
    public function getCode()
    {
        return $this->testData['code'] ?? null;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->testData['code'] = $code;
        return $this;
    }

    /**
     * Set data using method
     *
     * @param string $key
     * @param array $args
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setDataUsingMethod($key, $args = [])
    {
        return $this;
    }

    /**
     * Uses as default
     *
     * @return bool
     */
    public function usesAsDefault(): bool
    {
        return $this->testData['uses_as_default'] ?? false;
    }

    /**
     * Delete
     *
     * @return $this
     */
    public function delete(): self
    {
        return $this;
    }

    /**
     * Get collection
     *
     * @return mixed
     */
    public function getCollection()
    {
        return $this->testData['collection'] ?? null;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->testData['data'] ?? [];
        }
        return $this->testData['data'][$key] ?? null;
    }
}
