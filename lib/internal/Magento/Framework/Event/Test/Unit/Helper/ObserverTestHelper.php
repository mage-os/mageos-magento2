<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Event\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Test helper for Magento\Framework\Event\Observer
 */
class ObserverTestHelper extends Observer
{
    private $block = null;
    private $event = null;
    
    public function __construct($block = null, $event = null)
    {
        $this->block = $block;
        $this->event = $event;
    }
    
    /**
     * Set block
     * 
     * @param mixed $block
     * @return $this
     */
    public function setBlock($block)
    {
        $this->block = $block;
        return $this;
    }
    
    /**
     * Get block
     * 
     * @return mixed
     */
    public function getBlock()
    {
        return $this->block;
    }
    
    /**
     * Set event
     * 
     * @param mixed $event
     * @return $this
     */
    public function setEvent($event)
    {
        $this->event = $event;
        return $this;
    }
    
    /**
     * Get event
     * 
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }
}