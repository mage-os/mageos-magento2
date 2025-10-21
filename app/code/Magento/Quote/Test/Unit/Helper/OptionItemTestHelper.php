<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item\Option;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
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
    private $message = null;
    /** @var mixed */
    private $backorders = null;

    public function __construct()
    {
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

    public function setStockStateResult($stockStateResult)
    {
        $this->stockStateResult = $stockStateResult;
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

    public function getOption()
    {
        return $this->option;
    }

    public function setOption($option)
    {
        $this->option = $option;
        return $this;
    }

    public function setIsQtyDecimal($value)
    {
        $this->isQtyDecimal = $value;
        return $this;
    }

    public function getIsQtyDecimal()
    {
        return $this->isQtyDecimal;
    }

    public function setHasQtyOptionUpdate($value)
    {
        $this->hasQtyOptionUpdate = $value;
        return $this;
    }

    public function getHasQtyOptionUpdate()
    {
        return $this->hasQtyOptionUpdate;
    }

    public function setValue($value)
    {
        return $this->setData('value', $value);
    }

    public function getValue()
    {
        return $this->getData('value');
    }

    public function setMessage($value)
    {
        $this->message = $value;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setBackorders($value)
    {
        $this->backorders = $value;
        return $this;
    }

    public function getBackorders()
    {
        return $this->backorders;
    }
}
