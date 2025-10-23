<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Data\Form\Element\Test\Unit\Helper;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Test helper for AbstractElement that provides getContainer() functionality
 */
class AbstractElementTestHelper extends AbstractElement
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Fieldset|null
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
     * @return \Magento\Framework\Data\Form\Element\Fieldset|null
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Set container element
     *
     * @param \Magento\Framework\Data\Form\Element\Fieldset $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;
        return $this;
    }
}

