<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Pricing\Test\Unit\Helper;

use Magento\Framework\Pricing\SaleableInterface;

class SaleableTestHelper implements SaleableInterface
{
    /**
     * @return null
     */
    public function getTaxClassId()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getPriceInfo()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getTypeId()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return null;
    }

    /**
     * @return float
     */
    public function getQty()
    {
        return 1.0;
    }
}

