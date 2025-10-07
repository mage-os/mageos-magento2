<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Swatches\Test\Unit\Helper;

/**
 * Test helper for option objects
 */
class OptionTestHelper
{
    /**
     * Get ID
     *
     * @return int
     */
    public function getId()
    {
        return 14;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return 'Blue';
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return '#0000FF';
    }
}
