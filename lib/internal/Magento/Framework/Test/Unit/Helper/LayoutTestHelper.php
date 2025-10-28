<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Layout;

/**
 * Test helper for Layout with custom methods
 */
class LayoutTestHelper extends Layout
{
    /**
     * @var mixed
     */
    private $helperObject = null;

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get helper (custom method for tests)
     *
     * @param string $name
     * @return mixed
     */
    public function helper($name)
    {
        return $this->helperObject;
    }

    /**
     * Set helper object
     *
     * @param mixed $helper
     * @return $this
     */
    public function setHelper($helper): self
    {
        $this->helperObject = $helper;
        return $this;
    }
}
