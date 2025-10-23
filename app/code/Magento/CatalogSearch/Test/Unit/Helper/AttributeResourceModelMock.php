<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

/**
 * Mock class for AttributeResourceModel with additional methods
 */
class AttributeResourceModelMock extends Attribute
{
    private $searchWeight = null;

    /**
     * Mock method for getSearchWeight
     *
     * @return mixed
     */
    public function getSearchWeight()
    {
        return $this->searchWeight;
    }

    /**
     * Set the searchWeight value
     *
     * @param mixed $value
     * @return $this
     */
    public function setSearchWeight($value)
    {
        $this->searchWeight = $value;
        return $this;
    }

    /**
     * Required method from Attribute
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
