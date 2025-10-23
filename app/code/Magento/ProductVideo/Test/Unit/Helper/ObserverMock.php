<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ProductVideo\Test\Unit\Helper;

use Magento\Framework\Event\Observer;

/**
 * Mock class for Observer with getBlock method
 */
class ObserverMock extends Observer
{
    /**
     * Mock method for getBlock
     *
     * @return mixed
     */
    public function getBlock()
    {
        return $this->getData('block');
    }

    /**
     * Mock method for setBlock
     *
     * @param mixed $block
     * @return $this
     */
    public function setBlock($block)
    {
        return $this->setData('block', $block);
    }
}
