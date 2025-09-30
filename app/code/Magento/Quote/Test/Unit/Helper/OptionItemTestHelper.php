<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item\Option;

/**
 * TestHelper for OptionItem (actually Option)
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * Provides implementation for Option with additional test methods
 */
class OptionItemTestHelper extends Option
{
    /** @var bool */
    private $hasError = false;
    /** @var mixed */
    private $stockStateResult = null;
    /** @var mixed */
    private $qty = null;
    /** @var mixed */
    private $product = null;
    /** @var mixed */
    private $parentItem = null;
    /** @var mixed */
    private $option = null;
    /** @var mixed */
    private $isQtyDecimal = null;
    /** @var mixed */
    private $hasQtyOptionUpdate = null;
    /** @var mixed */
    private $value = null;
    /** @var mixed */
    private $message = null;
    /** @var mixed */
    private $backorders = null;
    /** @var array */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Get has error
     *
     * @return bool
     */
    public function getHasError()
    {
        return $this->hasError;
    }

    /**
     * Set has error
     *
     * @param bool $hasError
     * @return $this
     */
    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
        return $this;
    }

    /**
     * Get stock state result
     *
     * @return mixed
     */
    public function getStockStateResult()
    {
        return $this->stockStateResult;
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
     * Get qty
     *
     * @return mixed
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * Set qty
     *
     * @param mixed $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
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
     * Get option
     *
     * @return mixed
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set option
     *
     * @param mixed $option
     * @return $this
     */
    public function setOption($option)
    {
        $this->option = $option;
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
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Has data
     *
     * @param string $key
     * @return bool
     */
    public function hasData($key = '')
    {
        if ($key === '') {
            return !empty($this->data);
        }
        return isset($this->data[$key]);
    }

    /**
     * Set is qty decimal
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsQtyDecimal($value)
    {
        $this->isQtyDecimal = $value;
        return $this;
    }

    /**
     * Get is qty decimal
     *
     * @return mixed
     */
    public function getIsQtyDecimal()
    {
        return $this->isQtyDecimal;
    }

    /**
     * Set has qty option update
     *
     * @param mixed $value
     * @return $this
     */
    public function setHasQtyOptionUpdate($value)
    {
        $this->hasQtyOptionUpdate = $value;
        return $this;
    }

    /**
     * Get has qty option update
     *
     * @return mixed
     */
    public function getHasQtyOptionUpdate()
    {
        return $this->hasQtyOptionUpdate;
    }

    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set message
     *
     * @param mixed $value
     * @return $this
     */
    public function setMessage($value)
    {
        $this->message = $value;
        return $this;
    }

    /**
     * Get message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set backorders
     *
     * @param mixed $value
     * @return $this
     */
    public function setBackorders($value)
    {
        $this->backorders = $value;
        return $this;
    }

    /**
     * Get backorders
     *
     * @return mixed
     */
    public function getBackorders()
    {
        return $this->backorders;
    }
}
