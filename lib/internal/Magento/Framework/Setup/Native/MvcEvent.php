<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Framework\Setup\Native;

/**
 * Native MvcEvent that provides compatibility with Laminas\Mvc\MvcEvent
 */
class MvcEvent
{
    /**
     * @var MvcApplication
     */
    private $application;

    /**
     * Set application
     *
     * @param MvcApplication $application
     * @return self
     */
    public function setApplication(MvcApplication $application)
    {
        $this->application = $application;
        return $this;
    }

    /**
     * Get application
     *
     * @return MvcApplication
     */
    public function getApplication()
    {
        return $this->application;
    }
}