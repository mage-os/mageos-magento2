<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Framework\Data\Collection;

/**
 * Mock class for Collection with additional methods
 */
class CollectionTestHelper extends Collection
{
    /**
     * Mock method for addIdFilter
     *
     * @param mixed $ids
     * @return $this
     */
    public function addIdFilter($ids)
    {
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
