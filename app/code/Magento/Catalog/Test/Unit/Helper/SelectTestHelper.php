<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Option\Type\Select;

class SelectTestHelper extends Select
{
    /**
     * @var mixed
     */
    private $option = null;

    /**
     * @var mixed
     */
    private $configurationItemOption = null;

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
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getOptionPrice($value, $basePrice)
    {
        return $value;
    }
}

