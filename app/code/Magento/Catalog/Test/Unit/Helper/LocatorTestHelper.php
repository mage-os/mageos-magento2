<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Locator\RegistryLocator;

class LocatorTestHelper extends RegistryLocator
{
    /**
     * @var mixed
     */
    private $websiteIds;

    /**
     * @var mixed
     */
    private $product;

    /**
     * @var mixed
     */
    private $store;

    /**
     * @var mixed
     */
    private $baseCurrencyCode;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getWebsiteIds()
    {
        return $this->websiteIds;
    }

    /**
     * @param mixed $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds)
    {
        $this->websiteIds = $websiteIds;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * @param mixed $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->store = $store;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseCurrencyCode()
    {
        return $this->baseCurrencyCode;
    }

    /**
     * @param mixed $baseCurrencyCode
     * @return $this
     */
    public function setBaseCurrencyCode($baseCurrencyCode)
    {
        $this->baseCurrencyCode = $baseCurrencyCode;
        return $this;
    }
}

