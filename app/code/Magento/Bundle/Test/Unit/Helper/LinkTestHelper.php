<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Link;

/**
 * Test helper for Magento\Bundle\Model\Link
 *
 * Extends the Link class to add custom methods for testing
 */
class LinkTestHelper extends Link
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
        // Skip parent constructor
    }

    /**
     * Custom setSelectionId method for testing
     *
     * @param mixed $selectionId
     * @return self
     */
    public function setSelectionId($selectionId): self
    {
        $this->data['selection_id'] = $selectionId;
        return $this;
    }
}
