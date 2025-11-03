<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Pricing\Price\BasePrice;

class PriceInterfaceTestHelper extends BasePrice
{
    /**
     * @var mixed
     */
    private $valueReturn = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $return
     * @return $this
     */
    public function setValueReturn($return)
    {
        $this->valueReturn = $return;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->valueReturn;
    }
}

