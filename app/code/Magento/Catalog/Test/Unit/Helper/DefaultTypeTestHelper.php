<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Option\Type\DefaultType;

/**
 * Test helper for Magento\Catalog\Model\Product\Option\Type\DefaultType
 *
 * Extends DefaultType to add custom methods for testing
 */
class DefaultTypeTestHelper extends DefaultType
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Custom setRequest method for testing
     *
     * @param mixed $request
     * @return self
     */
    public function setRequest($request): self
    {
        $this->data['request'] = $request;
        return $this;
    }

    /**
     * Custom setProcessMode method for testing
     *
     * @param mixed $mode
     * @return self
     */
    public function setProcessMode($mode): self
    {
        $this->data['process_mode'] = $mode;
        return $this;
    }
}
