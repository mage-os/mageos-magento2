<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\ConfigurableProduct\Api\Data\OptionValueInterface;

/**
 * Test helper class for OptionValueInterface with custom methods
 */
class OptionValueInterfaceTestHelper implements OptionValueInterface
{
    /** @var array */
    private $data = [];

    /**
     * @inheritdoc
     */
    public function getValueIndex()
    {
        return $this->data['value_index'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setValueIndex($valueIndex)
    {
        $this->data['value_index'] = $valueIndex;
        return $this;
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
    public function setExtensionAttributes($extensionAttributes)
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }
}
