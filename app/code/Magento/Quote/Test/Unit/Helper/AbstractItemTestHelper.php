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
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @param $product
     * @param $children
     */
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

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->data['id'] ?? null;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setId($value): self
    {
        $this->data['id'] = $value;
        return $this;
    }

    /**
     * @return \Magento\Catalog\Model\Product|mixed|null
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * @param $product
     * @return $this
     */
    public function setProduct($product): self
    {
        $this->data['product'] = $product;
        return $this;
    }

    /**
     * @return \Magento\Quote\Model\Quote|mixed|null
     */
    public function getQuote()
    {
        return $this->data['quote'] ?? null;
    }

    /**
     * @param $quote
     * @return $this
     */
    public function setQuote($quote): self
    {
        $this->data['quote'] = $quote;
        return $this;
    }

    /**
     * @return AbstractItem|null
     */
    public function getParentItem(): ?AbstractItem
    {
        return $this->data['parent_item'] ?? null;
    }

    /**
     * @param $parentItem
     * @return $this
     */
    public function setParentItem($parentItem): self
    {
        $this->data['parent_item'] = $parentItem;
        return $this;
    }

    /**
     * @return array|AbstractItem[]
     */
    public function getChildren(): array
    {
        return $this->data['children'] ?? [];
    }

    /**
     * @param array $children
     * @return $this
     */
    public function setChildren(array $children): self
    {
        $this->data['children'] = $children;
        return $this;
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHasChildren(): bool
    {
        return !empty($this->data['children']);
    }

    /**
     * @return float
     */
    public function getQty(): float
    {
        return $this->data['qty'] ?? 1.0;
    }

    /**
     * @param float $qty
     * @return $this
     */
    public function setQty(float $qty): self
    {
        $this->data['qty'] = $qty;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProductType(): ?string
    {
        return $this->data['product_type'] ?? null;
    }

    /**
     * @param string $productType
     * @return $this
     */
    public function setProductType(string $productType): self
    {
        $this->data['product_type'] = $productType;
        return $this;
    }

    /**
     * @param string $code
     * @return \Magento\Catalog\Model\Product\Configuration\Item\Option\OptionInterface|mixed|null
     */
    public function getOptionByCode($code)
    {
        return $this->data['options'][$code] ?? null;
    }

    /**
     * @param string $code
     * @param $value
     * @return $this
     */
    public function setOption(string $code, $value): self
    {
        $this->data['options'][$code] = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->data['options'] ?? [];
    }

    /**
     * @param array $options
     * @return $this
     */
    public function setOptions(array $options): self
    {
        $this->data['options'] = $options;
        return $this;
    }

    /**
     * @param $product
     * @return bool
     */
    public function representProduct($product): bool
    {
        return $this->getProduct() === $product;
    }

    /**
     * @param AbstractItem $item
     * @return bool
     */
    public function compare(AbstractItem $item): bool
    {
        return $this->getId() === $item->getId();
    }

    /**
     * @param $isDeleted
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isDeleted($isDeleted = null): bool
    {
        return $this->data['is_deleted'] ?? false;
    }

    /**
     * @param bool $flag
     * @return $this
     */
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
