<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Setup\Mvc;

/**
 * Native MvcEvent class that provides minimal compatibility with Laminas MvcEvent
 * for Magento setup commands without requiring Laminas MVC
 */
class MvcEvent
{
    public const EVENT_BOOTSTRAP = 'bootstrap';
    /**
     * @var MvcApplication
     */
    private MvcApplication $application;

    /**
     * Set application
     *
     * @param MvcApplication $application
     * @return void
     */
    public function setApplication(MvcApplication $application): void
    {
        $this->application = $application;
    }

    /**
     * Get application
     *
     * @return MvcApplication
     */
    public function getApplication(): MvcApplication
    {
        return $this->application;
    }
}
