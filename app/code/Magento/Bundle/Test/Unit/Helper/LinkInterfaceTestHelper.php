<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Api\Data\LinkInterface;

/**
 * Test helper class for Bundle LinkInterface with custom methods
 *
 * This helper implements LinkInterface and adds custom methods
 * that don't exist on the interface for testing purposes.
 */
class LinkInterfaceTestHelper implements LinkInterface
{
    /**
     * @var array Data storage for properties
     */
    private $data = [];

    /**
     * @inheritdoc
     */
    public function getSku(): ?string
    {
        return $this->data['sku'] ?? null;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSku($sku): self
    {
        $this->data['sku'] = $sku;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptionId(): ?int
    {
        return $this->data['option_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setOptionId($optionId): self
    {
        $this->data['option_id'] = $optionId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getQty(): ?float
    {
        return $this->data['qty'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setQty($qty): self
    {
        $this->data['qty'] = $qty;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition(): ?int
    {
        return $this->data['position'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position): self
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setId($id): self
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsDefault(): ?bool
    {
        return $this->data['is_default'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function isDefault(): ?bool
    {
        return $this->getIsDefault();
    }

    /**
     * @inheritdoc
     */
    public function setIsDefault($isDefault): self
    {
        $this->data['is_default'] = $isDefault;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriceType(): ?int
    {
        return $this->data['price_type'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setPriceType($priceType): self
    {
        $this->data['price_type'] = $priceType;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): ?float
    {
        return $this->data['price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price): self
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCanChangeQuantity(): ?bool
    {
        return $this->data['can_change_quantity'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function canChangeQuantity(): ?bool
    {
        return $this->getCanChangeQuantity();
    }

    /**
     * @inheritdoc
     */
    public function setCanChangeQuantity($canChangeQuantity): self
    {
        $this->data['can_change_quantity'] = $canChangeQuantity;
        return $this;
    }

    /**
     * Custom getSelectionId method for testing
     *
     * @return int|null
     */
    public function getSelectionId(): ?int
    {
        return $this->data['selection_id'] ?? null;
    }

    /**
     * Set selection ID for testing
     *
     * @param int|null $selectionId
     * @return self
     */
    public function setSelectionId(?int $selectionId): self
    {
        $this->data['selection_id'] = $selectionId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->data['extension_attributes'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes($extensionAttributes): self
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }

    /**
     * Generic data setter for flexible testing
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Generic data getter for flexible testing
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getTestData(string $key, $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }
}
