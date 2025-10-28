<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Test helper for AbstractElement that provides getContainer() functionality
 */
class AbstractElementTestHelper extends AbstractElement
{
    /**
     * @var Fieldset|null
     */
    private $container;

    /**
     * Constructor that skips parent initialization
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get container element
     *
     * @return Fieldset|null
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set container element
     *
     * @param Fieldset $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }
}

