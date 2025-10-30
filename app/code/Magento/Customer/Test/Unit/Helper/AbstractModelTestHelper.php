<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Framework\Model\AbstractModel;

/**
 * Test helper for AbstractModel with custom methods
 */
class AbstractModelTestHelper extends AbstractModel
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
     * Get is customer save transaction
     *
     * @return bool|null
     */
    public function getIsCustomerSaveTransaction()
    {
        return $this->testData['is_customer_save_transaction'] ?? null;
    }

    /**
     * Set is customer save transaction
     *
     * @param bool $value
     * @return $this
     */
    public function setIsCustomerSaveTransaction($value): self
    {
        $this->testData['is_customer_save_transaction'] = $value;
        return $this;
    }

    /**
     * Get ID
     *
     * @return int|string|null
     */
    public function getId()
    {
        return $this->testData['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int|string|null $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->testData['id'] = $id;
        return $this;
    }

    /**
     * Get resource
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->testData['resource'] ?? null;
    }

    /**
     * Set resource
     *
     * @param mixed $resource
     * @return $this
     */
    public function setResource($resource): self
    {
        $this->testData['resource'] = $resource;
        return $this;
    }
}
