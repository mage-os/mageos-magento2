<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\BundleImportExport\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Bundle Selection Product with selection-specific methods
 */
class SelectionProductTestHelper extends Product
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get SKU for testing
     *
     * @return string|int
     */
    public function getSku()
    {
        return $this->data['sku'] ?? 1;
    }


    /**
     * Get selection price value for testing
     *
     * @return float|int
     */
    public function getSelectionPriceValue()
    {
        return $this->data['selection_price_value'] ?? 1;
    }

    /**
     * Set selection price value for testing
     *
     * @param float|int $priceValue
     * @return self
     */
    public function setSelectionPriceValue($priceValue): self
    {
        $this->data['selection_price_value'] = $priceValue;
        return $this;
    }

    /**
     * Get selection quantity for testing
     *
     * @return float|int
     */
    public function getSelectionQty()
    {
        return $this->data['selection_qty'] ?? 1;
    }

    /**
     * Set selection quantity for testing
     *
     * @param float|int $qty
     * @return self
     */
    public function setSelectionQty($qty): self
    {
        $this->data['selection_qty'] = $qty;
        return $this;
    }

    /**
     * Get selection price type for testing
     *
     * @return int
     */
    public function getSelectionPriceType(): int
    {
        return $this->data['selection_price_type'] ?? 1;
    }

    /**
     * Set selection price type for testing
     *
     * @param int $priceType
     * @return self
     */
    public function setSelectionPriceType(int $priceType): self
    {
        $this->data['selection_price_type'] = $priceType;
        return $this;
    }

    /**
     * Get selection can change quantity for testing
     *
     * @return int
     */
    public function getSelectionCanChangeQty(): int
    {
        return $this->data['selection_can_change_qty'] ?? 1;
    }

    /**
     * Set selection can change quantity for testing
     *
     * @param int $canChangeQty
     * @return self
     */
    public function setSelectionCanChangeQty(int $canChangeQty): self
    {
        $this->data['selection_can_change_qty'] = $canChangeQty;
        return $this;
    }

    /**
     * Get is default for testing
     *
     * @return mixed
     */
    public function getIsDefault()
    {
        return $this->data['is_default'] ?? null;
    }

    /**
     * Set is default for testing
     *
     * @param mixed $isDefault
     * @return self
     */
    public function setIsDefault($isDefault): self
    {
        $this->data['is_default'] = $isDefault;
        return $this;
    }
}
