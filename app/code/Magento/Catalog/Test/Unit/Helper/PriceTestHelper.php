<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Type\Price;

/**
 * Test helper
 */
class PriceTestHelper extends Price
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var mixed
     */
    private $priceId = null;

    /**
     * @var mixed
     */
    private $price = null;

    /**
     * @var mixed
     */
    private $resource = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get selection final total price for testing
     *
     * @param mixed $product
     * @param mixed $qty
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getSelectionFinalTotalPrice($product = null, $qty = null)
    {
        return $this->data['selection_final_total_price'] ?? $this;
    }

    /**
     * Get total prices for testing
     *
     * @param mixed $product
     * @param mixed $which
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getTotalPrices($product = null, $which = null)
    {
        return $this->data['total_prices'] ?? null;
    }

    /**
     * Set total prices for testing
     *
     * @param mixed $value
     * @return self
     */
    public function setTotalPrices($value): self
    {
        $this->data['total_prices'] = $value;
        return $this;
    }

    /**
     * Set price ID (for DataProvider compatibility)
     *
     * @param mixed $priceId
     * @return $this
     */
    public function setPriceId($priceId)
    {
        $this->priceId = $priceId;
        return $this;
    }

    /**
     * Get price (for DataProvider compatibility)
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set price (for DataProvider compatibility)
     *
     * @param mixed $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
    }

    /**
     * Get resource (for DataProvider compatibility)
     *
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set resource (for DataProvider compatibility)
     *
     * @param mixed $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;
        return $this;
    }
}
