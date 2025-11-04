<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item;

/**
 * Test helper for AbstractItem mocking
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AbstractItemTestHelper extends Item
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * @param mixed $product
     * @param mixed $children
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
     * @return \Magento\Catalog\Model\Product|mixed|null
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * @param mixed $product
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
     * @param mixed $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->data['quote'] = $quote;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->data['address'] ?? null;
    }

    /**
     * @param mixed $address
     * @return $this
     */
    public function setAddress($address)
    {
        $this->data['address'] = $address;
        return $this;
    }

    /**
     * @param mixed $parentItem
     * @return $this
     */
    public function setParentItem($parentItem): self
    {
        $this->data['parent_item'] = $parentItem;
        return $this;
    }

    /**
     * @return array
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
     * @param mixed $code
     * @return mixed
     */
    public function getOptionByCode($code)
    {
        return null;
    }
}
