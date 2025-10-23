<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection;

/**
 * Mock class for AbstractCollection with additional methods
 */
class AbstractCollectionMock extends AbstractCollection
{
    private $idFilter = null;

    /**
     * Mock method for addIdFilter
     *
     * @param mixed $idFilter
     * @return $this
     */
    public function addIdFilter($idFilter)
    {
        $this->idFilter = $idFilter;
        return $this;
    }

    /**
     * Get the id filter
     *
     * @return mixed
     */
    public function getIdFilter()
    {
        return $this->idFilter;
    }

    /**
     * Required method from AbstractCollection
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
