<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Quote\Test\Unit\Model\GuestCart;

use Magento\Quote\Model\QuoteIdMask;

class GuestCartQuoteIdMaskDouble extends QuoteIdMask
{
    private $quoteIdValue = null;
    private $maskedIdValue = null;

    public function __construct()
    {
        // Skip parent constructor to avoid framework dependencies
    }

    public function setQuoteId($quoteId)
    {
        $this->quoteIdValue = $quoteId;
        return $this;
    }

    public function getQuoteId()
    {
        return $this->quoteIdValue;
    }

    public function load($id, $field = null)
    {
        $this->maskedIdValue = is_scalar($id) ? (string)$id : $this->maskedIdValue;
        return $this;
    }

    public function save()
    {
        return $this;
    }

    public function getMaskedId()
    {
        return $this->maskedIdValue ?? '';
    }

    public function setMaskedId(string $id)
    {
        $this->maskedIdValue = $id;
        return $this;
    }
}



