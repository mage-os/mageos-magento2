<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute;

/**
 * Mock class for EAV Attribute with additional methods
 */
class AttributeMock extends Attribute
{
    private $isFilterable = null;
    private $searchWeight = null;

    /**
     * Mock method for getIsFilterable
     *
     * @return mixed
     */
    public function getIsFilterable()
    {
        return $this->isFilterable;
    }

    /**
     * Set the isFilterable value
     *
     * @param mixed $value
     * @return $this
     */
    public function setIsFilterable($value)
    {
        $this->isFilterable = $value;
        return $this;
    }

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
