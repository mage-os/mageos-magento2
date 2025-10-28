<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

/**
 * Mock class for ProductCollection with consecutive calls support
 */
class ProductCollectionTestHelper extends Collection
{
    /**
     * @var mixed
     */
    private $allIdsSequence = [];
    /**
     * @var mixed
     */
    private $allIdsCallCount = 0;

    /**
     * Mock method for getAllIds with sequence support
     *
     * @param int|null $limit
     * @param int|null $offset
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAllIds($limit = null, $offset = null)
    {
        if (isset($this->allIdsSequence[$this->allIdsCallCount])) {
            $result = $this->allIdsSequence[$this->allIdsCallCount];
            $this->allIdsCallCount++;
            return $result;
        }
        return [];
    }

    /**
     * Set the sequence for getAllIds calls
     *
     * @param array $sequence
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setAllIdsSequence(array $sequence)
    {
        $this->allIdsSequence = $sequence;
        $this->allIdsCallCount = 0;
        return $this;
    }

    /**
     * Required method from Collection
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}

