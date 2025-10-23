<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Pricing\Test\Unit\Helper;

use Magento\Framework\Pricing\PriceInfo\Base;

/**
 * Test helper for PriceInfo Base
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PriceInfoBaseTestHelper extends Base
{
    /**
     * @var mixed
     */
    private $extensionAttributes = null;

    /**
     * @var mixed
     */
    private $price = null;

    public function __construct()
    {
    }

    public function getExtensionAttributes()
    {
        return $this->extensionAttributes;
    }

    public function setExtensionAttributes($value): self
    {
        $this->extensionAttributes = $value;
        return $this;
    }

    public function getPrice($priceCode)
    {
        return $this->price;
    }

    public function setPrice($value): self
    {
        $this->price = $value;
        return $this;
    }
}
