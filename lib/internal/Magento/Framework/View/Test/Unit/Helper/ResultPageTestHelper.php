<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\View\Test\Unit\Helper;

use Magento\Framework\View\Result\Page;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ResultPageTestHelper extends Page
{
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    public function getConfig()
    {
        return $this;
    }

    public function getLayout()
    {
        return $this;
    }

    public function setActiveMenu($menuId)
    {
        return $this;
    }

    public function addBreadcrumb($label, $title, $link = null)
    {
        return $this;
    }

    public function getBlock($name)
    {
        return $this;
    }

    public function getTitle()
    {
        return $this;
    }

    public function prepend($element)
    {
        return $this;
    }
}

