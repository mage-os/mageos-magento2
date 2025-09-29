<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item;

/**
 * TestHelper for Quote Item with validator-specific dynamic methods
 */
class QuoteItemTestHelperForValidator extends Item
{
    /** @var int|null */
    private $productId = null;
    /** @var bool */
    private $hasError = false;
    /** @var mixed */
    private $stockStateResult = null;
    /** @var mixed */
    private $quote = null;
    /** @var float|null */
    private $qty = null;
    /** @var mixed */
    private $product = null;
    /** @var mixed */
    private $parentItem = null;
    /** @var array */
    private $data = [];
    /** @var mixed */
    private $qtyOptions = null;
    /** @var int|null */
    private $itemId = null;
    /** @var array */
    private $errorInfos = [];

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getProductId()
    {
        return $this->productId;
    }

    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }

    public function getHasError()
    {
        return $this->hasError;
    }

    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
        return $this;
    }

    public function getStockStateResult()
    {
        return $this->stockStateResult;
    }

    public function setStockStateResult($result)
    {
        $this->stockStateResult = $result;
        return $this;
    }

    public function getQuote()
    {
        return $this->quote;
    }

    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    public function getQty()
    {
        return $this->qty;
    }

    public function setQty($qty)
    {
        $this->qty = $qty;
        return $this;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
        return $this;
    }

    public function getParentItem()
    {
        return $this->parentItem;
    }

    public function setParentItem($parentItem)
    {
        $this->parentItem = $parentItem;
        return $this;
    }

    public function addErrorInfo($origin = null, $code = null, $message = null, $additionalData = null)
    {
        $this->errorInfos[] = [
            'origin' => $origin,
            'code' => $code,
            'message' => $message,
            'additionalData' => $additionalData
        ];
        return $this;
    }

    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    public function getQtyOptions()
    {
        return $this->qtyOptions;
    }

    public function setQtyOptions($options)
    {
        $this->qtyOptions = $options;
        return $this;
    }

    public function getItemId()
    {
        return $this->itemId;
    }

    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
        return $this;
    }
}
