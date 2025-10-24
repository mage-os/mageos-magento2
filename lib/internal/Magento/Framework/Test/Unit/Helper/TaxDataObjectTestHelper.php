<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for DataObject with tax-related methods
 *
 * This helper extends the concrete DataObject class to provide
 * test-specific functionality for tax change detection.
 */
class TaxDataObjectTestHelper extends DataObject
{
    /**
     * @var bool
     */
    private $flag;

    /**
     * Constructor that accepts flag value
     *
     * @param bool $flag
     */
    public function __construct($flag)
    {
        parent::__construct();
        $this->flag = $flag;
    }

    /**
     * Get whether tax has changed
     *
     * @return bool
     */
    public function getIsTaxChanged()
    {
        return $this->flag;
    }

    /**
     * Get whether shipping tax has changed
     *
     * @return bool
     */
    public function getIsShippingTaxChanged()
    {
        return $this->flag;
    }
}

