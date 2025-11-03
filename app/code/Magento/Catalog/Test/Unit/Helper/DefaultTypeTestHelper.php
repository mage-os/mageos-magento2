<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Option\Type\DefaultType;

class DefaultTypeTestHelper extends DefaultType
{
    /**
     * @var mixed
     */
    private $option = null;

    /**
     * @var mixed
     */
    private $configurationItem = null;

    /**
     * @var mixed
     */
    private $configurationItemOption = null;

    /**
     * @var mixed
     */
    private $valueReturn = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $option
     * @return $this
     */
    public function setOption($option)
    {
        $this->option = $option;
        return $this;
    }

    /**
     * @param mixed $configurationItem
     * @return $this
     */
    public function setConfigurationItem($configurationItem)
    {
        $this->configurationItem = $configurationItem;
        return $this;
    }

    /**
     * @param mixed $configurationItemOption
     * @return $this
     */
    public function setConfigurationItemOption($configurationItemOption)
    {
        $this->configurationItemOption = $configurationItemOption;
        return $this;
    }

    /**
     * @param mixed $value
     * @param mixed $basePrice
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOptionPrice($value, $basePrice)
    {
        return 10.0;
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

