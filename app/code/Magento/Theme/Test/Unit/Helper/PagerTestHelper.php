<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Theme\Test\Unit\Helper;

use Magento\Theme\Block\Html\Pager;

/**
 * Test helper for Pager with additional test methods
 */
class PagerTestHelper extends Pager
{
    /**
     * Bypass parent constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set use container
     *
     * @param bool $flag
     * @return $this
     */
    public function setUseContainer($flag)
    {
        return $this->setData('use_container', $flag);
    }

    /**
     * Set show amounts
     *
     * @param bool $flag
     * @return $this
     */
    public function setShowAmounts($flag)
    {
        return $this->setData('show_amounts', $flag);
    }

    /**
     * Set total limit
     *
     * @param int $limit
     * @return $this
     */
    public function setTotalLimit($limit)
    {
        return $this->setData('total_limit', $limit);
    }
}