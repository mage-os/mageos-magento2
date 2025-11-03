<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\PageCache\Test\Unit\Helper;

use Magento\PageCache\Model\Config;

class ConfigTestHelper extends Config
{
    /**
     * @var bool
     */
    private $isEnabledReturn = false;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param bool $return
     * @return $this
     */
    public function setIsEnabledReturn($return)
    {
        $this->isEnabledReturn = $return;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->isEnabledReturn;
    }
}

