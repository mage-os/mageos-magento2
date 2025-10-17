<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Test\Unit\Helper;

use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test helper for ContextExtensionInterface
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
     * Set is customer flag
     *
     * @param bool $isCustomer
     * @return $this
     */
    public function setIsCustomer($isCustomer): self
    {
        $this->isCustomer = (bool) $isCustomer;
        return $this;
    }

    /**
     * Get is customer flag
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
     * @param StoreInterface $store
     * @return $this
     */
    public function setStore($store): self
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
     * @param int|null $customerGroupId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setCustomerGroupId($customerGroupId): self
    {
        return $this;
    }
}
