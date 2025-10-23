<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Helper;

use Magento\Framework\Api\Search\SearchCriteriaBuilder;

/**
 * Mock class for SearchCriteriaBuilder with additional methods
 */
class SearchCriteriaBuilderTestHelper extends SearchCriteriaBuilder
{
    /**
     * @var mixed
     */
    private $requestName = null;

    /**
     * Mock method for setRequestName
     *
     * @param string $requestName
     * @return $this
     */
    public function setRequestName($requestName)
    {
        $this->requestName = $requestName;
        return $this;
    }

    /**
     * Get the request name
     *
     * @return string|null
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * Mock method for addFilter - handles both signatures
     *
     * @param mixed $fieldOrFilter
     * @param mixed $value
     * @param string $conditionType
     * @return $this
     */
    public function addFilter($fieldOrFilter, $value = null, $conditionType = 'eq')
    {
        return $this;
    }

    /**
     * Mock method for create
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function create()
    {
        return new \Magento\Framework\Api\Search\SearchCriteria();
    }

    /**
     * Required method from SearchCriteriaBuilder
     */
    protected function _construct(): void
    {
        // Mock implementation
    }
}
