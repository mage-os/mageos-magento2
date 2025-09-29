<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Test\Unit\Helper;

use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Test\Unit\Helper\StoreTestHelper;

/**
 * Test helper for ContextExtensionInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ContextExtensionInterfaceTestHelper implements ContextExtensionInterface
{
    /**
     * @var bool
     */
    private bool $isCustomer = false;
    
    /**
     * @var StoreInterface|null
     */
    private ?StoreInterface $store = null;

    /**
     * @var int|null
     */
    private ?int $storeId = null;

    public function setIsCustomer($isCustomer): self
    {
        $this->isCustomer = (bool) $isCustomer;
        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsCustomer(): bool
    {
        return $this->isCustomer;
    }

    public function getStore(): ?StoreInterface
    {
        // If we have a store ID but no store object, create a simple store object
        if ($this->storeId !== null && $this->store === null) {
            return $this->createSimpleStoreObject($this->storeId);
        }
        return $this->store;
    }

    /**
     * Create a simple store object that returns the store ID
     *
     * @param int $storeId
     * @return StoreInterface
     */
    private function createSimpleStoreObject(int $storeId): StoreInterface
    {
        return new StoreTestHelper($storeId);
    }

    /**
     * Get store ID (for when store was set as integer)
     *
     * @return int|null
     */
    public function getStoreId(): ?int
    {
        return $this->storeId;
    }

    public function setStore($store): self
    {
        // Handle both StoreInterface objects and store IDs (integers)
        if (is_int($store)) {
            // Store the ID for later use
            $this->storeId = $store;
            $this->store = null; // Clear store object when using ID
        } else {
            $this->store = $store;
            $this->storeId = null; // Clear store ID when using store object
        }
        return $this;
    }

    public function getCustomerGroupId(): ?int
    {
        return null;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCustomerGroupId($customerGroupId): self
    {
        return $this;
    }
}
