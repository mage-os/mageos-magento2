<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\GraphQl\Test\Unit\Helper;

use Magento\GraphQl\Model\Query\ContextExtensionInterface;

/**
 * Test helper for ContextExtension to support extension attribute methods
 */
class ContextExtensionTestHelper implements ContextExtensionInterface
{
    /**
     * @var \Magento\Store\Api\Data\StoreInterface|null
     */
    private $store;

    /**
     * Get store
     *
     * @return \Magento\Store\Api\Data\StoreInterface|null
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set store
     *
     * @param \Magento\Store\Api\Data\StoreInterface|null $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }
}