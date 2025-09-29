<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item;

/**
 * TestHelper for Quote Item
 * Provides implementation for Quote Item with additional test methods
 */
class QuoteItemTestHelper extends Item
{
    /** @var bool|null */
    private $isQtyDecimal = null;
    /** @var bool|null */
    private $useOldQty = null;
    /** @var int|null */
    private $backorders = null;
    /** @var mixed */
    private $stockStateResult = null;
    /** @var mixed */
    private $parentItem = null;
    /** @var mixed */
    private $product = null;
    /** @var int|null */
    private $id = null;
    /** @var int|null */
    private $quoteId = null;
    /** @var array */
    private $data = [];
    /** @var string|null */
    private $message = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Set is qty decimal
     *
     * @param bool|null $isQtyDecimal
     * @return $this
     */
    public function setIsQtyDecimal($isQtyDecimal)
    {
        $this->isQtyDecimal = $isQtyDecimal;
        return $this;
    }

    /**
     * Set use old qty
     *
     * @param bool|null $useOldQty
     * @return $this
     */
    public function setUseOldQty($useOldQty)
    {
        $this->useOldQty = $useOldQty;
        return $this;
    }

    /**
     * Set backorders
     *
     * @param int|null $backorders
     * @return $this
     */
    public function setBackorders($backorders)
    {
        $this->backorders = $backorders;
        return $this;
    }

    /**
     * Set stock state result
     *
     * @param mixed $stockStateResult
     * @return $this
     */
    public function setStockStateResult($stockStateResult)
    {
        $this->stockStateResult = $stockStateResult;
        return $this;
    }

    /**
     * Get parent item
     *
     * @return mixed
     */
    public function getParentItem()
    {
        return $this->parentItem;
    }

    /**
     * Set parent item
     *
     * @param mixed $parentItem
     * @return $this
     */
    public function setParentItem($parentItem)
    {
        $this->parentItem = $parentItem;
        return $this;
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set product
     *
     * @param mixed $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get quote id
     *
     * @return int|null
     */
    public function getQuoteId()
    {
        return $this->quoteId;
    }

    /**
     * Set quote id
     *
     * @param int|null $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        $this->quoteId = $quoteId;
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Get message
     *
     * @return string|null
     */
    public function getMessage($string = true)
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param string|null $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get qty to add
     *
     * @return float|null
     */
    public function getQtyToAdd()
    {
        return $this->data['qty_to_add'] ?? null;
    }

    /**
     * Set qty to add
     *
     * @param float|null $qtyToAdd
     * @return $this
     */
    public function setQtyToAdd($qtyToAdd)
    {
        $this->data['qty_to_add'] = $qtyToAdd;
        return $this;
    }

    /**
     * Override setQty to avoid _prepareQty dependency
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
     * Get qty
     *
     * @return float|null
     */
    public function getQty()
    {
        return $this->data['qty'] ?? null;
    }

    /**
     * Update qty option
     *
     * @param mixed $option
     * @param mixed $qty
     * @return $this
     */
    public function updateQtyOption($option, $qty)
    {
        return $this;
    }

    /**
     * Get store
     *
     * @return mixed
     */
    public function getStore()
    {
        return $this->data['store'] ?? null;
    }

    /**
     * Set store
     *
     * @param mixed $store
     * @return $this
     */
    public function setStore($store)
    {
        $this->data['store'] = $store;
        return $this;
    }
}
