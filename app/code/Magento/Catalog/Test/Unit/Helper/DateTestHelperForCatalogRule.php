<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * TestHelper for Date with dynamic methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DateTestHelperForCatalogRule extends DateTime
{
    /** @var string|null */
    private $formatValue = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function format($format)
    {
        return $this->formatValue;
    }

    public function setFormatValue($value)
    {
        $this->formatValue = $value;
        return $this;
    }
}
