<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * TestHelper for Product with dynamic methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductTestHelperForCatalogRule extends Product
{
    /** @var int|null */
    private $websiteId = null;
    /** @var int|null */
    private $customerGroupId = null;
    /** @var int|null */
    private $storeId = null;
    /** @var int|null */
    private $id = null;
    /** @var array */
    private $data = [];
    /** @var float|null */
    private $finalPrice = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    // Dynamic methods from addMethods
    public function getWebsiteId()
    {
        return $this->websiteId;
    }

    public function setWebsiteId($value)
    {
        $this->websiteId = $value;
        return $this;
    }

    public function getCustomerGroupId()
    {
        return $this->customerGroupId;
    }

    public function setCustomerGroupId($value)
    {
        $this->customerGroupId = $value;
        return $this;
    }

    // Methods from onlyMethods - MUST match parent signatures exactly
    public function getStoreId()
    {
        return $this->storeId;
    }

    public function setStoreId($value)
    {
        $this->storeId = $value;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    public function getData($keyword = '', $index = null)
    {
        if ($keyword === '') {
            return $this->data;
        }
        return isset($this->data[$keyword]) ? $this->data[$keyword] : null;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function setFinalPrice($value)
    {
        $this->finalPrice = $value;
        return $this;
    }

    public function getFinalPrice($qty = null)
    {
        return $this->finalPrice;
    }
}
