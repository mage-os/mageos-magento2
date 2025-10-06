<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Test\Unit\Helper;

use Magento\GraphQl\Model\Query\ContextExtensionInterface;
use Magento\Store\Api\Data\StoreInterface;

class ContextExtensionGetStoreTestHelper implements ContextExtensionInterface
{
    private $store;
    private $isCustomer;
    private $customerGroupId;

    /**
     * @return StoreInterface|null
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param StoreInterface $store
     * @return $this
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getIsCustomer()
    {
        return $this->isCustomer;
    }

    /**
     * @param bool|null $isCustomer
     * @return $this
     */
    public function setIsCustomer($isCustomer)
    {
        $this->isCustomer = $isCustomer;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    /**
     * @param int|null $groupId
     * @return $this
     */
    public function setCustomerGroupId($groupId)
    {
        $this->customerGroupId = $groupId;
        return $this;
    }
}


