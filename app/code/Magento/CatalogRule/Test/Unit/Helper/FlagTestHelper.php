<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogRule\Test\Unit\Helper;

use Magento\CatalogRule\Model\Flag;

/**
 * TestHelper for Flag with dynamic methods
 */
class FlagTestHelper extends Flag
{
    /** @var mixed */
    private $state = null;

    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($value)
    {
        $this->state = $value;
        return $this;
    }
}
