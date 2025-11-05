<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Model\StoreManager;

class StoreManagerTestHelper extends StoreManager
{
    /**
     * @var mixed
     */
    private $store = null;

    /**
     * @var string
     */
    private $code = '';

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $storeId
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getStore($storeId = null)
    {
        return $this->store;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setStore($value)
    {
        $this->store = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setCode($value)
    {
        $this->code = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setStoreReturn($value)
    {
        $this->store = $value;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setDefaultStoreViewReturn($value)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultStoreView()
    {
        return $this->store;
    }
}

