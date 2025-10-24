<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Model\Session;

/**
 * Test helper for Session
 *
 * This helper extends the concrete Session class to provide
 * test-specific functionality without dependency injection issues.
 */
class SessionTestHelper extends Session
{
    /**
     * Constructor without dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set URL notice flag
     *
     * @param bool $flag
     * @return $this
     */
    public function setIsUrlNotice($flag)
    {
        return $this;
    }
}

