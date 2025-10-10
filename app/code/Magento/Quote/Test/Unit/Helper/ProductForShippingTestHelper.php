<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

/**
 * Minimal product helper for Shipping tests providing only required APIs.
 */
class ProductForShippingTestHelper
{
    /** @var int */
    private $id = 1;

    /**
     * No-op setter used by quote item flow.
     *
     * @param float|int $price
     * @return $this
     */
    public function setFinalPrice($price)
    {
        return $this;
    }

    /**
     * Return a fixed product id for tests.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return a fixed product type id for tests.
     *
     * @return string
     */
    public function getTypeId()
    {
        return 'simple';
    }

    /**
     * No-op setter for custom options expected by quote item internals.
     *
     * @param array $options
     * @return $this
     */
    public function setCustomOptions($options)
    {
        return $this;
    }

    /**
     * Return a fixed SKU for tests.
     *
     * @return string
     */
    public function getSku()
    {
        return 'test-sku';
    }

    /**
     * Return a fixed product name for tests.
     *
     * @return string
     */
    public function getName()
    {
        return 'Test Product';
    }

    /**
     * Return a fixed weight for tests.
     *
     * @return float|int
     */
    public function getWeight()
    {
        return 1;
    }

    /**
     * Return a fixed tax class id for tests.
     *
     * @return int
     */
    public function getTaxClassId()
    {
        return 0;
    }

    /**
     * Return a fixed cost for tests.
     *
     * @return float|int
     */
    public function getCost()
    {
        return 0;
    }

    /**
     * Whether product is virtual.
     *
     * @return bool
     */
    public function isVirtual()
    {
        return false;
    }

    /**
     * Return minimal extension attributes with stock item provider.
     *
     * @return ProductForShippingTestHelperExtensionAttributes
     */
    public function getExtensionAttributes()
    {
        return new ProductForShippingTestHelperExtensionAttributes();
    }
}

/**
 * Minimal extension attributes carrying stock item for tests.
 */
class ProductForShippingTestHelperExtensionAttributes
{
    /**
     * Return minimal stock item helper.
     *
     * @return ProductForShippingTestHelperStockItem
     */
    public function getStockItem()
    {
        return new ProductForShippingTestHelperStockItem();
    }
}

/**
 * Minimal stock item helper with methods sometimes read by quote item flow.
 */
class ProductForShippingTestHelperStockItem
{
    /**
     * Whether qty is decimal.
     *
     * @return bool
     */
    public function getIsQtyDecimal()
    {
        return false;
    }

    /**
     * Qty increments value.
     *
     * @return int
     */
    public function getQtyIncrements()
    {
        return 1;
    }
}


