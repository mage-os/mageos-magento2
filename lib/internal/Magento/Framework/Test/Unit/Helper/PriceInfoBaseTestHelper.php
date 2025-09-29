<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

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

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get price
     *
     * @param mixed $priceCode
     * @return mixed
     */
    public function getPrice($priceCode)
    {
        return $this->price;
    }

    /**
     * Set price
     *
     * @param mixed $value
     * @return $this
     */
    public function setPrice($value): self
    {
        $this->price = $value;
        return $this;
    }

    /**
     * Get extension attributes
     *
     * @return mixed
     */
    public function getExtensionAttributes()
    {
        return $this->extensionAttributes;
    }

    /**
     * Set extension attributes
     *
     * @param mixed $value
     * @return $this
     */
    public function setExtensionAttributes($value): self
    {
        $this->extensionAttributes = $value;
        return $this;
    }
}
