<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product\Type\Simple;

/**
 * Test helper for Catalog Product Type Simple
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductTypeSimpleTestHelper extends Simple
{
    /**
     * @var array
     */
    private array $ids = [];

    /**
     * @var int
     */
    private int $callCount = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set IDs to return on successive getId() calls
     *
     * @param array $ids
     * @return $this
     */
    public function setIds(array $ids)
    {
        $this->ids = $ids;
        $this->callCount = 0;
        return $this;
    }

    /**
     * Get ID - returns different ID on each call based on call count
     *
     * @return mixed
     */
    public function getId()
    {
        $id = $this->ids[$this->callCount] ?? null;
        $this->callCount++;
        return $id;
    }
}
