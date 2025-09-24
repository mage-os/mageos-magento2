<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\WishlistGraphQl\Test\Unit\Mock;

use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test helper class for ContextExtensionInterface used across WishlistGraphQl tests
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
     * Set customer flag
     *
     * @param mixed $isCustomer
     * @return self
     */
    public function setIsCustomer($isCustomer): self
    {
        $this->isCustomer = (bool) $isCustomer;
        return $this;
    }
    
    /**
     * Get customer flag
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsCustomer(): bool
    {
        return $this->isCustomer;
    }
    
    /**
     * Get store
     *
     * @return StoreInterface|null
     */
    public function getStore(): ?StoreInterface
    {
        return $this->store;
    }
    
    /**
     * Set store
     *
     * @param StoreInterface|null $store
     * @return self
     */
    public function setStore(?StoreInterface $store): self
    {
        $this->store = $store;
        return $this;
    }
    
    /**
     * Get customer group ID
     *
     * @return int|null
     */
    public function getCustomerGroupId(): ?int
    {
        return null;
    }
    
    /**
     * Set customer group ID
     *
     * @param mixed $customerGroupId
     * @return self
     */
    public function setCustomerGroupId($customerGroupId): self
    {
        return $this;
    }
}
