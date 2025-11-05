<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Quote\Model\Quote;

/**
 * Test helper for Quote
 */
class QuoteTestHelper extends Quote
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * getStoreId (custom method for testing)
     *
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->data['storeId'] ?? null;
    }

    /**
     * getQuoteId (custom method for testing)
     *
     * @return mixed
     */
    public function getQuoteId()
    {
        return $this->data['quoteId'] ?? null;
    }

    /**
     * setQuoteId (custom method for testing)
     *
     * @param mixed $value
     * @return $this
     */
    public function setQuoteId($value)
    {
        $this->data['quoteId'] = $value;
        return $this;
    }

    /**
     * hasCustomerId (custom method for testing)
     *
     * @return bool
     */
    public function hasCustomerId()
    {
        return $this->data['customerId'] ?? false;
    }

    /**
     * getCustomerId (custom method for testing)
     *
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->data['customerId'] ?? null;
    }
}
