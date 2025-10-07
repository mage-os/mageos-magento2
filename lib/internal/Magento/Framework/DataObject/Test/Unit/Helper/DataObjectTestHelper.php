<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\DataObject\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for DataObject class
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * @var mixed
     */
    private $types;

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Get types
     *
     * @return mixed
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * Set types
     *
     * @param mixed $types
     * @return void
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }
}
