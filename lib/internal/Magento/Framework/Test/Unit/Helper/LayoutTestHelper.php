<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Result\Layout;

/**
 * Mock class for Layout with additional methods
 */
class LayoutTestHelper extends Layout
{
    /**
     * Mock method for getUpdate
     *
     * @return \Magento\Framework\View\Model\Layout\Merge
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Set the update object
     *
     * @param \Magento\Framework\View\Model\Layout\Merge $update
     * @return $this
     */
    public function setUpdate($update)
    {
        $this->update = $update;
        return $this;
    }

    /**
     * Required method from Layout
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}

