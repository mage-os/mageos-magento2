<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Test helper class for Quote used across Quote and related module tests
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class QuoteTestHelper extends Quote
{
    /** @var int */
    public int $customerId = 1;

    /** @var array */
    public array $items = [];

    /** @var bool */
    private $inventoryProcessed = false;

    /** @var bool */
    private $hasError = false;

    /** @var bool */
    private $isSuperMode = false;

    /** @var mixed */
    private $quote = null;

    /** @var array */
    private $itemsCollection = [];

    /** @var array */
    private $errorInfos = [];

    public function __construct()
    {
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getItemById($itemId)
    {
        return $this->items[$itemId] ?? false;
    }

    public function removeItem($itemId)
    {
        unset($this->items[$itemId]);
        return $this;
    }

    public function setHasError($flag): self
    {
        $this->hasError = $flag;
        return $this;
    }

    public function __wakeup(): void
    {
    }

    public function getInventoryProcessed()
    {
        return $this->inventoryProcessed;
    }

    public function setInventoryProcessed($inventoryProcessed)
    {
        $this->inventoryProcessed = $inventoryProcessed;
        return $this;
    }

    public function getHasError()
    {
        return $this->hasError;
    }

    public function getIsSuperMode()
    {
        return $this->isSuperMode;
    }

    public function setIsSuperMode($isSuperMode)
    {
        $this->isSuperMode = $isSuperMode;
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

    public function getItemsCollection($useCache = true)
    {
        return $this->itemsCollection;
    }

    public function setItemsCollection($itemsCollection)
    {
        $this->itemsCollection = $itemsCollection;
        return $this;
    }

    public function getErrorInfos()
    {
        return $this->errorInfos;
    }

    public function setErrorInfos($errorInfos)
    {
        $this->errorInfos = $errorInfos;
        return $this;
    }

    public function removeErrorInfosByParams($origin, $params)
    {
        return $this;
    }

    public function addErrorInfo(
        $type = 'error',
        $origin = null,
        $code = null,
        $message = null,
        $additionalData = null
    ) {
        $this->errorInfos[] = [
            'type' => $type,
            'origin' => $origin,
            'code' => $code,
            'message' => $message,
            'additionalData' => $additionalData
        ];
        return $this;
    }
}
