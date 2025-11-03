<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Event\Test\Unit\Helper;

use Magento\Framework\Event;

/**
 * Mock class for Event with adapter and bunch methods
 */
class EventTestHelper extends Event
{
    /**
     * Mock method for getAdapter
     *
     * @return mixed
     */
    public function getAdapter()
    {
        return $this->getData('adapter');
    }

    /**
     * Mock method for getBunch
     *
     * @return mixed
     */
    public function getBunch()
    {
        return $this->getData('bunch');
    }

    /**
     * Mock method for getCategory
     *
     * @return mixed
     */
    public function getCategory()
    {
        return $this->getData('category');
    }

    /**
     * Mock method for getProduct
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->getData('product');
    }
}
