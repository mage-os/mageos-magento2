<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Test helper for Observer class
 */
class ObserverTestHelper extends Observer
{
    private $block;

    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get block (custom method for testing)
     *
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set block (custom method for testing)
     *
     * @param mixed $block
     * @return $this
     */
    public function setBlock($block): self
    {
        $this->block = $block;
        return $this;
    }

    /**
     * @var mixed
     */
    private $form;

    /**
     * Get form (custom method for testing)
     *
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * Set form (custom method for testing)
     *
     * @param mixed $form
     * @return self
     */
    public function setForm($form): self
    {
        $this->form = $form;
        return $this;
    }
}
