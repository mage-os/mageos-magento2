<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Result\Page;

/**
 * Test helper for Page with custom methods
 */
class PageTestHelper extends Page
{
    /**
     * @var string|null
     */
    private $activeMenu = null;

    /**
     * @var array
     */
    private $breadcrumbs = [];

    /**
     * Constructor that skips parent dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set active menu (custom method for tests)
     *
     * @param string $menu
     * @return $this
     */
    public function setActiveMenu(string $menu): self
    {
        $this->activeMenu = $menu;
        return $this;
    }

    /**
     * Get active menu
     *
     * @return string|null
     */
    public function getActiveMenu(): ?string
    {
        return $this->activeMenu;
    }

    /**
     * Add breadcrumb (custom method for tests)
     *
     * @param string $label
     * @param string $title
     * @return $this
     */
    public function addBreadcrumb(string $label, string $title): self
    {
        $this->breadcrumbs[] = ['label' => $label, 'title' => $title];
        return $this;
    }

    /**
     * Get breadcrumbs
     *
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}

