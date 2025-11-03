<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Stdlib\Test\Unit\Helper;

use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * TestHelper for DateTime with custom format override
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class DateTimeTestHelper extends DateTime
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
