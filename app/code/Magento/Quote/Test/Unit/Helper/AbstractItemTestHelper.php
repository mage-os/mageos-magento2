<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Address;

/**
 * Test helper for AbstractItem mocking
 */
class AbstractItemTestHelper extends AbstractItem
{
    private array $data = [];

    public function __construct($product = null, $children = null)
    {
        // Skip parent constructor to avoid dependencies
        if ($product) {
            $this->setProduct($product);
        }
        if ($children) {
            $this->setChildren($children);
        }
    }

    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    public function setId(int $id): self
    {
        $this->data['id'] = $id;
        return $this;
    }

    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    public function setProduct($product): self
    {
        $this->data['product'] = $product;
        return $this;
    }

    public function getQuote()
    {
        return $this->data['quote'] ?? null;
    }

    public function setQuote($quote): self
    {
        $this->data['quote'] = $quote;
        return $this;
    }

    public function getParentItem(): ?AbstractItem
    {
        return $this->data['parent_item'] ?? null;
    }

    public function setParentItem($parentItem): self
    {
        $this->data['parent_item'] = $parentItem;
        return $this;
    }

    public function getChildren(): array
    {
        return $this->data['children'] ?? [];
    }

    public function setChildren(array $children): self
    {
        $this->data['children'] = $children;
        return $this;
    }

    public function getHasChildren(): bool
    {
        return !empty($this->data['children']);
    }

    public function getQty(): float
    {
        return $this->data['qty'] ?? 1.0;
    }

    public function setQty(float $qty): self
    {
        $this->data['qty'] = $qty;
        return $this;
    }

    public function getProductType(): ?string
    {
        return $this->data['product_type'] ?? null;
    }

    public function setProductType(string $productType): self
    {
        $this->data['product_type'] = $productType;
        return $this;
    }

    public function getOptionByCode(string $code)
    {
        return $this->data['options'][$code] ?? null;
    }

    public function setOption(string $code, $value): self
    {
        $this->data['options'][$code] = $value;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->data['options'] ?? [];
    }

    public function setOptions(array $options): self
    {
        $this->data['options'] = $options;
        return $this;
    }

    public function representProduct($product): bool
    {
        return $this->getProduct() === $product;
    }

    public function compare(AbstractItem $item): bool
    {
        return $this->getId() === $item->getId();
    }

    public function isDeleted($isDeleted = null): bool
    {
        return $this->data['is_deleted'] ?? false;
    }

    public function setIsDeleted(bool $flag): self
    {
        $this->data['is_deleted'] = $flag;
        return $this;
    }

    /**
     * Return the quote address for the item.
     *
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->data['address'] ?? null;
    }

    /**
     * Set quote address for the item (useful for testing)
     *
     * @param Address $address
     * @return $this
     */
    public function setAddress(Address $address): self
    {
        $this->data['address'] = $address;
        return $this;
    }
}
