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
 *
 * WHY THIS HELPER EXISTS:
 * - Observer extends DataObject which has __call magic methods
 * - Observer already has explicit setEvent()/getEvent() methods
 * - setBlock()/getBlock() work automatically via DataObject's __call
 * - This helper provides explicit methods for better IDE support and type clarity
 * - Used by multiple test files in magento2ee for observer mocking
 *
 * NOTE: Technically redundant as all methods exist in parent or via __call,
 * but kept for backward compatibility and explicit method signatures in tests.
 */
class ObserverTestHelper extends Observer
{
    /**
     * @var mixed
     */
    private $block = null;
    
    /**
     * @var mixed
     */
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
