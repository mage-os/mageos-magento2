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
     * Set IDs
     *
     * @param array $ids
     * @return $this
     */
    public function setIds(array $ids)
    {
        $this->ids = $ids;
        return $this;
    }

    /**
     * Get IDs
     *
     * @return array
     */
    public function getIds(): array
    {
        return $this->ids;
    }

    /**
     * Get call count
     *
     * @return int
     */
    public function getCallCount(): int
    {
        return $this->callCount;
    }

    /**
     * Set call count
     *
     * @param int $callCount
     * @return $this
     */
    public function setCallCount(int $callCount): self
    {
        $this->callCount = $callCount;
        return $this;
    }

    /**
     * Increment call count
     *
     * @return $this
     */
    public function incrementCallCount(): self
    {
        $this->callCount++;
        return $this;
    }

    /**
     * Get ID (with call count logic)
     *
     * @return mixed
     */
    public function getId()
    {
        $id = $this->ids[$this->callCount] ?? null;
        $this->callCount++;
        return $id;
    }

    /**
     * Set ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setId($id): self
    {
        $this->ids[] = $id;
        return $this;
    }
}
