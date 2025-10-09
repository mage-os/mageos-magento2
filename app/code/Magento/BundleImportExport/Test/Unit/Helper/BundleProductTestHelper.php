<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\BundleImportExport\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Bundle Product with bundle-specific methods
 */
class BundleProductTestHelper extends Product
{
    /**
     * @var mixed
     */
    private $optionsCollection;

    /**
     * @var mixed
     */
    private $selectionsCollection;

    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * @param mixed $optionsCollection
     * @param mixed $selectionsCollection
     */
    public function __construct($optionsCollection = null, $selectionsCollection = null)
    {
        $this->optionsCollection = $optionsCollection;
        $this->selectionsCollection = $selectionsCollection;
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get store IDs for testing
     *
     * @return array
     */
    public function getStoreIds(): array
    {
        return $this->data['store_ids'] ?? [1];
    }

    /**
     * Set store IDs for testing
     *
     * @param array $storeIds
     * @return self
     */
    public function setStoreIds(array $storeIds): self
    {
        $this->data['store_ids'] = $storeIds;
        return $this;
    }

    /**
     * Get entity ID for testing
     *
     * @return int
     */
    public function getEntityId(): int
    {
        return $this->data['entity_id'] ?? 1;
    }

    /**
     * Get price type for testing
     *
     * @return int
     */
    public function getPriceType(): int
    {
        return $this->data['price_type'] ?? 1;
    }

    /**
     * Set price type for testing
     *
     * @param int $priceType
     * @return self
     */
    public function setPriceType(int $priceType): self
    {
        $this->data['price_type'] = $priceType;
        return $this;
    }

    /**
     * Get shipment type for testing
     *
     * @return int
     */
    public function getShipmentType(): int
    {
        return $this->data['shipment_type'] ?? 1;
    }

    /**
     * Set shipment type for testing
     *
     * @param int $shipmentType
     * @return self
     */
    public function setShipmentType(int $shipmentType): self
    {
        $this->data['shipment_type'] = $shipmentType;
        return $this;
    }

    /**
     * Get SKU type for testing
     *
     * @return int
     */
    public function getSkuType(): int
    {
        return $this->data['sku_type'] ?? 1;
    }

    /**
     * Set SKU type for testing
     *
     * @param int $skuType
     * @return self
     */
    public function setSkuType(int $skuType): self
    {
        $this->data['sku_type'] = $skuType;
        return $this;
    }

    /**
     * Get price view for testing
     *
     * @return int
     */
    public function getPriceView(): int
    {
        return $this->data['price_view'] ?? 1;
    }

    /**
     * Set price view for testing
     *
     * @param int $priceView
     * @return self
     */
    public function setPriceView(int $priceView): self
    {
        $this->data['price_view'] = $priceView;
        return $this;
    }

    /**
     * Get weight type for testing
     *
     * @return int
     */
    public function getWeightType(): int
    {
        return $this->data['weight_type'] ?? 1;
    }

    /**
     * Set weight type for testing
     *
     * @param int $weightType
     * @return self
     */
    public function setWeightType(int $weightType): self
    {
        $this->data['weight_type'] = $weightType;
        return $this;
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
     * Get type instance for testing (returns self)
     *
     * @return self
     */
    public function getTypeInstance(): self
    {
        return $this;
    }

    /**
     * Get options collection for testing
     *
     * @param mixed $product
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOptionsCollection($product = null)
    {
        return $this->optionsCollection;
    }

    /**
     * Set options collection for testing
     *
     * @param mixed $optionsCollection
     * @return self
     */
    public function setOptionsCollection($optionsCollection): self
    {
        $this->optionsCollection = $optionsCollection;
        return $this;
    }

    /**
     * Get selections collection for testing
     *
     * @param array|null $optionIds
     * @param mixed $product
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getSelectionsCollection($optionIds = null, $product = null)
    {
        return $this->selectionsCollection;
    }

    /**
     * Set selections collection for testing
     *
     * @param mixed $selectionsCollection
     * @return self
     */
    public function setSelectionsCollection($selectionsCollection): self
    {
        $this->selectionsCollection = $selectionsCollection;
        return $this;
    }
}
