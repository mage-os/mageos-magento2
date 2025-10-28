<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\View\Model\Layout\Merge;
use Magento\Framework\View\Result\Layout;

/**
 * Mock class for Layout with additional methods
 */
class LayoutTestHelper extends Layout
{
    /**
     * @var Merge
     */
    private $update;

    /**
     * Mock method for getUpdate
     *
     * @return Merge
     */
    public function getUpdate()
    {
        return $this->update;
    }

    /**
     * Set the update object
     *
     * @param Merge $update
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
