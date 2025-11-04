<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Link;

/**
 * Test helper for Product\Link
 */
class ProductLinkTestHelper extends Link
{
    /**
     * @var string
     */
    private $linkedProductId;

    /**
     * @var array
     */
    private $arrayData = [];

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
        // Intentionally empty - skip parent constructor for testing
    }

    /**
     * Get linked product ID
     *
     * @return string
     */
    public function getLinkedProductId()
    {
        return $this->linkedProductId;
    }

    /**
     * Set linked product ID
     *
     * @param string $id
     * @return void
     */
    public function setLinkedProductId($id)
    {
        $this->linkedProductId = $id;
    }

    /**
     * Convert to array
     *
     * @param array $keys
     * @return array
     */
    public function toArray(array $keys = [])
    {
        return $this->arrayData;
    }

    /**
     * Set array data
     *
     * @param array $data
     * @return void
     */
    public function setArrayData(array $data)
    {
        $this->arrayData = $data;
    }
}

