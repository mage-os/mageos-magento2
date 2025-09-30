<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductRender\FormattedPriceInfoInterface;
use Magento\Catalog\Api\Data\ProductRender\PriceInfoExtensionInterface;
use Magento\Catalog\Api\Data\ProductRender\PriceInfoInterface;

/**
 * Test helper for ProductRender PriceInfoInterface
 * 
 * Provides a testable implementation of PriceInfoInterface with support for:
 * - Custom getPrice() method (not in interface)
 * - All required interface methods
 * - Fluent interface pattern for easy test setup
 */
class PriceInfoInterfaceTestHelper implements PriceInfoInterface
{
    /**
     * @var array Internal data storage
     */
    private array $data = [];

    /**
     * Custom getPrice method for testing (not in interface)
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->data['price'] ?? null;
    }

    /**
     * Set price for testing
     *
     * @param mixed $price
     * @return self
     */
    public function setPrice($price): self
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFinalPrice()
    {
        return $this->data['final_price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setFinalPrice($finalPrice)
    {
        $this->data['final_price'] = $finalPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMaxPrice()
    {
        return $this->data['max_price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setMaxPrice($maxPrice)
    {
        $this->data['max_price'] = $maxPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setMaxRegularPrice($maxRegularPrice)
    {
        $this->data['max_regular_price'] = $maxRegularPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMaxRegularPrice()
    {
        return $this->data['max_regular_price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setMinimalRegularPrice($minRegularPrice)
    {
        $this->data['minimal_regular_price'] = $minRegularPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMinimalRegularPrice()
    {
        return $this->data['minimal_regular_price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setSpecialPrice($specialPrice)
    {
        $this->data['special_price'] = $specialPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSpecialPrice()
    {
        return $this->data['special_price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function getMinimalPrice()
    {
        return $this->data['minimal_price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setMinimalPrice($minimalPrice)
    {
        $this->data['minimal_price'] = $minimalPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRegularPrice()
    {
        return $this->data['regular_price'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setRegularPrice($regularPrice)
    {
        $this->data['regular_price'] = $regularPrice;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getFormattedPrices()
    {
        return $this->data['formatted_prices'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setFormattedPrices(FormattedPriceInfoInterface $formattedPriceInfo)
    {
        $this->data['formatted_prices'] = $formattedPriceInfo;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->data['extension_attributes'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(?PriceInfoExtensionInterface $extensionAttributes = null)
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }
}
