<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper for Product class in ItemCarrier tests
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductCarrierTestHelper extends Product
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $disableAddToCart = false;

    /**
     * Constructor
     *
     * @param string|null $name
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get product URL
     *
     * @param mixed $useSid
     * @return string
     */
    public function getProductUrl($useSid = null)
    {
        return 'http://example.com/product';
    }

    /**
     * Set disable add to cart
     *
     * @param bool $value
     * @return $this
     */
    public function setDisableAddToCart($value)
    {
        $this->disableAddToCart = $value;
        return $this;
    }

    /**
     * Get disable add to cart
     *
     * @return bool
     */
    public function isDisableAddToCart()
    {
        return $this->disableAddToCart;
    }

    /**
     * Get disable add to cart (alias for compatibility)
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getDisableAddToCart()
    {
        return $this->disableAddToCart;
    }
}
