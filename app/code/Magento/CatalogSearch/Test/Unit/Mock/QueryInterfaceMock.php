<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogSearch\Test\Unit\Mock;

use Magento\Framework\Search\Request\QueryInterface;

/**
 * Mock class for QueryInterface with additional methods
 */
class QueryInterfaceMock implements QueryInterface
{
    private $must = [];
    private $should = [];

    /**
     * Mock method for getMust
     *
     * @return array
     */
    public function getMust()
    {
        return $this->must;
    }

    /**
     * Set the must value
     *
     * @param array $value
     * @return $this
     */
    public function setMust($value)
    {
        $this->must = $value;
        return $this;
    }

    /**
     * Mock method for getShould
     *
     * @return array
     */
    public function getShould()
    {
        return $this->should;
    }

    /**
     * Set the should value
     *
     * @param array $value
     * @return $this
     */
    public function setShould($value)
    {
        $this->should = $value;
        return $this;
    }

    // Required methods from QueryInterface
    public function getType() { return null; }
    public function setName($name) { return $this; }
    public function getName() { return null; }
    public function getBoost() { return null; }
    public function setBoost($boost) { return $this; }
}
