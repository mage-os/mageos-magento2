<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item\AbstractItem;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class AbstractItemTestHelper extends AbstractItem
{
    private $quote = null;
    private $address = null;

    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
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

    public function getAddress()
    {
        return $this->address;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function getOptionByCode($code)
    {
        return null;
    }
}

