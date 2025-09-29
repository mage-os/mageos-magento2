<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * TestHelper for Quote with validator-specific dynamic methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QuoteTestHelperForValidator extends Quote
{
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
        // Skip parent constructor to avoid complex dependencies
    }

    public function hasError()
    {
        return $this->hasError;
    }

    public function setHasError($hasError)
    {
        $this->hasError = $hasError;
        return $this;
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

    public function setItemsCollection($items)
    {
        $this->itemsCollection = $items;
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
