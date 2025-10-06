<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Quote\Test\Unit\Helper;

use Magento\Quote\Model\Quote\Item;

/**
 * Quote item helper exposing getQtyToAdd and getPreviousQty for PHPUnit 12/10.
 */
class QuoteItemQtyMethodsTestHelper extends Item
{
    public function __construct()
    {
    }

    public function getQtyToAdd()
    {
        return null;
    }

    public function getPreviousQty()
    {
        return null;
    }
}






