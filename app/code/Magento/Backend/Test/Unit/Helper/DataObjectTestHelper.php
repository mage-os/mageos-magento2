<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for DataObject with custom methods
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // Initialize with empty data array
        parent::__construct([]);
    }

    /**
     * Get item ID
     *
     * @return mixed
     */
    public function getItemId()
    {
        return $this->getData('item_id');
    }
}

