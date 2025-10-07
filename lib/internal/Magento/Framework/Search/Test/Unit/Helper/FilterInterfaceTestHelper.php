<?php
/**
 * Copyright 2024 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Search\Test\Unit\Helper;

use Magento\Framework\Search\Request\FilterInterface;

/**
 * Test helper for FilterInterface
 * Provides getMust(), getShould(), getMustNot() methods that don't exist on the interface
 */
class FilterInterfaceTestHelper implements FilterInterface
{
    /**
     * @var string
     */
    private $type = 'termFilter';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var array
     */
    private $must = [];

    /**
     * @var array
     */
    private $should = [];

    /**
     * @var array
     */
    private $mustNot = [];

    /**
     * Skip constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get must filters
     *
     * @return array
     */
    public function getMust(): array
    {
        return $this->must;
    }

    /**
     * Set must filters
     *
     * @param array $must
     * @return $this
     */
    public function setMust(array $must): self
    {
        $this->must = $must;
        return $this;
    }

    /**
     * Get should filters
     *
     * @return array
     */
    public function getShould(): array
    {
        return $this->should;
    }

    /**
     * Set should filters
     *
     * @param array $should
     * @return $this
     */
    public function setShould(array $should): self
    {
        $this->should = $should;
        return $this;
    }

    /**
     * Get must not filters
     *
     * @return array
     */
    public function getMustNot(): array
    {
        return $this->mustNot;
    }

    /**
     * Set must not filters
     *
     * @param array $mustNot
     * @return $this
     */
    public function setMustNot(array $mustNot): self
    {
        $this->mustNot = $mustNot;
        return $this;
    }
}
