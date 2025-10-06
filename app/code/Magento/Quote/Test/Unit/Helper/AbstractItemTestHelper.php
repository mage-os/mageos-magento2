<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * Test helper for AbstractItem mocking
 */
class AbstractItemTestHelper extends AbstractItem
{
    private $data = [];

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
     * Get item ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set item ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Get product
     *
     * @return \Magento\Catalog\Model\Product|null
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * Set product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->data['product'] = $product;
        return $this;
    }

    /**
     * Get quote
     *
     * @return \Magento\Quote\Model\Quote|null
     */
    public function getQuote()
    {
        return $this->data['quote'] ?? null;
    }

    /**
     * Set quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->data['quote'] = $quote;
        return $this;
    }

    /**
     * Get parent item
     *
     * @return AbstractItem|null
     */
    public function getParentItem()
    {
        return $this->data['parent_item'] ?? null;
    }

    /**
     * Set parent item
     *
     * @param AbstractItem $parentItem
     * @return $this
     */
    public function setParentItem($parentItem)
    {
        $this->data['parent_item'] = $parentItem;
        return $this;
    }

    /**
     * Get children items
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->data['children'] ?? [];
    }

    /**
     * Set children items
     *
     * @param array $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->data['children'] = $children;
        return $this;
    }

    /**
     * Check if item has children
     *
     * @return bool
     */
    public function getHasChildren()
    {
        return !empty($this->data['children']);
    }

    /**
     * Get quantity
     *
     * @return float
     */
    public function getQty()
    {
        return $this->data['qty'] ?? 1.0;
    }

    /**
     * Set quantity
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->data['qty'] = $qty;
        return $this;
    }

    /**
     * Get product type
     *
     * @return string|null
     */
    public function getProductType()
    {
        return $this->data['product_type'] ?? null;
    }

    /**
     * Set product type
     *
     * @param string $productType
     * @return $this
     */
    public function setProductType($productType)
    {
        $this->data['product_type'] = $productType;
        return $this;
    }

    /**
     * Get option by code
     *
     * @param string $code
     * @return mixed|null
     */
    public function getOptionByCode($code)
    {
        return $this->data['options'][$code] ?? null;
    }

    /**
     * Set option
     *
     * @param string $code
     * @param mixed $value
     * @return $this
     */
    public function setOption($code, $value)
    {
        $this->data['options'][$code] = $value;
        return $this;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->data['options'] ?? [];
    }

    /**
     * Set all options
     *
     * @param array $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->data['options'] = $options;
        return $this;
    }

    /**
     * Check if item represents product
     *
     * @return bool
     */
    public function representProduct($product)
    {
        return $this->getProduct() === $product;
    }

    /**
     * Compare item
     *
     * @param AbstractItem $item
     * @return bool
     */
    public function compare($item)
    {
        return $this->getId() === $item->getId();
    }

    /**
     * Check if item is deleted
     *
     * @param null $isDeleted
     * @return bool
     */
    public function isDeleted($isDeleted = null): bool
    {
        return $this->data['is_deleted'] ?? false;
    }

    /**
     * Set deleted flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsDeleted($flag)
    {
        $this->data['is_deleted'] = $flag;
        return $this;
    }

    public function getAddress()
    {
        // TODO: Implement getAddress() method.
    }
}
