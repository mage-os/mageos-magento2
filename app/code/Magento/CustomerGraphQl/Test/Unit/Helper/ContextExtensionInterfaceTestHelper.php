<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CustomerGraphQl\Test\Unit\Helper;

use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\Store\Api\Data\StoreInterface;

/**
 * Test helper class for ContextExtensionInterface used across GraphQl tests
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
        return $this->store;
    }

    public function setStore(?StoreInterface $store): self
    {
        $this->store = $store;
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
