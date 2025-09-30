<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Extended TestHelper for Quote
 * Provides implementation for Quote with additional test methods beyond the base QuoteTestHelper
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class QuoteTestHelperExtended extends Quote
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

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Check if has error
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
     * Check if is super mode
     *
     * @return bool
     */
    public function getIsSuperMode()
    {
        return $this->isSuperMode;
    }

    /**
     * Set is super mode
     *
     * @param bool $isSuperMode
     * @return $this
     */
    public function setIsSuperMode($isSuperMode)
    {
        $this->isSuperMode = $isSuperMode;
        return $this;
    }

    /**
     * Get quote
     *
     * @return mixed
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * Set quote
     *
     * @param mixed $quote
     * @return $this
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * Get items collection
     *
     * @return array
     */
    public function getItemsCollection($useCache = true)
    {
        return $this->itemsCollection;
    }

    /**
     * Set items collection
     *
     * @param array $itemsCollection
     * @return $this
     */
    public function setItemsCollection($itemsCollection)
    {
        $this->itemsCollection = $itemsCollection;
        return $this;
    }

    /**
     * Get error infos
     *
     * @return array
     */
    public function getErrorInfos()
    {
        return $this->errorInfos;
    }

    /**
     * Set error infos
     *
     * @param array $errorInfos
     * @return $this
     */
    public function setErrorInfos($errorInfos)
    {
        $this->errorInfos = $errorInfos;
        return $this;
    }
}
