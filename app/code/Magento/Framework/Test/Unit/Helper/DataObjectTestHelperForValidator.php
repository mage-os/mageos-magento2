<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * TestHelper for DataObject with validator-specific dynamic methods
 */
class DataObjectTestHelperForValidator extends DataObject
{
    /** @var bool|null */
    private $checkQtyIncrements = null;
    /** @var string|null */
    private $message = null;
    /** @var string|null */
    private $quoteMessage = null;
    /** @var bool */
    private $hasError = false;
    /** @var int|null */
    private $quoteMessageIndex = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function checkQtyIncrements()
    {
        return $this->checkQtyIncrements;
    }

    public function setCheckQtyIncrements($value)
    {
        $this->checkQtyIncrements = $value;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getQuoteMessage()
    {
        return $this->quoteMessage;
    }

    public function setQuoteMessage($message)
    {
        $this->quoteMessage = $message;
        return $this;
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

    public function getQuoteMessageIndex()
    {
        return $this->quoteMessageIndex;
    }

    public function setQuoteMessageIndex($index)
    {
        $this->quoteMessageIndex = $index;
        return $this;
    }
}
