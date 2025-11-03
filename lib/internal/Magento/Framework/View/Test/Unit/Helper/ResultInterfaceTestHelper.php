<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\View\Test\Unit\Helper;

use Magento\Framework\View\Result\Layout;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ResultInterfaceTestHelper extends Layout
{
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function forward($action)
    {
        return $this;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }
}

