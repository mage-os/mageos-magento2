<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\Frontend\DefaultFrontend;

/**
 * Test helper for Magento\Eav\Model\Entity\Attribute\Frontend\DefaultFrontend
 *
 * Extends the concrete DefaultFrontend class to add custom methods for testing
 */
class FrontendTestHelper extends DefaultFrontend
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor - skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Override getInputType for testing
     *
     * @return string
     */
    public function getInputType()
    {
        return $this->data['input_type'] ?? 'input_type';
    }
}
