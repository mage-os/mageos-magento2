<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Model\StoreManager;

/**
 * Test helper for Store Manager
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StoreManagerTestHelper extends StoreManager
{
    /**
     * @var mixed
     */
    private $store = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get store
     *
     * @param mixed $storeId
     * @return mixed
     */
    public function getStore($storeId = null)
    {
        return $this->store;
    }

    /**
     * Set store
     *
     * @param mixed $store
     * @return $this
     */
    public function setStore($store): self
    {
        $this->store = $store;
        return $this;
    }

    /**
     * Wakeup method
     *
     * @return $this
     */
    public function __wakeup()
    {
        return $this;
    }
}
