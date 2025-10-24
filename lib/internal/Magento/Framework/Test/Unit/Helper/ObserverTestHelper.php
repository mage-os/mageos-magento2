<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Test helper for Observer
 *
 * This helper extends the concrete Observer class to provide
 * test-specific functionality without dependency injection issues.
 */
class ObserverTestHelper extends Observer
{
    /**
     * @var mixed
     */
    private $controllerAction;

    /**
     * Constructor that accepts controller action
     *
     * @param mixed $controllerAction
     */
    public function __construct($controllerAction)
    {
        $this->controllerAction = $controllerAction;
    }

    /**
     * Get controller action
     *
     * @return mixed
     */
    public function getControllerAction()
    {
        return $this->controllerAction;
    }

    /**
     * Set controller action
     *
     * @param mixed $action
     * @return $this
     */
    public function setControllerAction($action)
    {
        $this->controllerAction = $action;
        return $this;
    }
}

