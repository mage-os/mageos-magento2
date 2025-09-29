<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Element\Template\Context;

/**
 * Test helper for View Element Template Context
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ViewElementTemplateContextTestHelper extends Context
{
    /**
     * @var mixed
     */
    private $storeConfig = null;

    /**
     * @var mixed
     */
    private $eventManager = null;

    /**
     * @var mixed
     */
    private $scopeConfig = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get store config
     *
     * @param mixed $path
     * @return mixed
     */
    public function getStoreConfig($path)
    {
        return $this->storeConfig;
    }

    /**
     * Set store config
     *
     * @param mixed $config
     * @return $this
     */
    public function setStoreConfig($config): self
    {
        $this->storeConfig = $config;
        return $this;
    }

    /**
     * Get event manager
     *
     * @return mixed
     */
    public function getEventManager()
    {
        return $this->eventManager;
    }

    /**
     * Set event manager
     *
     * @param mixed $manager
     * @return $this
     */
    public function setEventManager($manager): self
    {
        $this->eventManager = $manager;
        return $this;
    }

    /**
     * Get scope config
     *
     * @return mixed
     */
    public function getScopeConfig()
    {
        return $this->scopeConfig;
    }

    /**
     * Set scope config
     *
     * @param mixed $config
     * @return $this
     */
    public function setScopeConfig($config): self
    {
        $this->scopeConfig = $config;
        return $this;
    }
}
