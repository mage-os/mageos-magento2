<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable\OptionValue;

/**
 * Test helper class for OptionValueInterface with custom methods
 *
 * Extends OptionValue class to leverage existing OptionValueInterface implementation
 * Following PHPUnit 12 migration rules: extend existing implementation instead of implementing interface
 */
class OptionValueTestHelper extends OptionValue
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent dependencies for clean test initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Custom method for testing pricing value
     *
     * @return mixed
     */
    public function getPricingValue()
    {
        return $this->data['pricing_value'] ?? null;
    }

    /**
     * Custom method for testing pricing value
     *
     * @param mixed $pricingValue
     * @return self
     */
    public function setPricingValue($pricingValue)
    {
        $this->data['pricing_value'] = $pricingValue;
        return $this;
    }

    /**
     * Custom method for testing is percent
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsPercent()
    {
        return $this->data['is_percent'] ?? false;
    }

    /**
     * Custom method for testing is percent
     *
     * @param bool $isPercent
     * @return self
     */
    public function setIsPercent($isPercent)
    {
        $this->data['is_percent'] = $isPercent;
        return $this;
    }
}
