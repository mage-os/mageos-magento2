<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\Data\Tree\Node;

/**
 * Test helper for Node class
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class NodeTestHelper extends Node
{
    /**
     * @var bool
     */
    private $hasChildren = false;

    /**
     * @var string
     */
    private $idField = 'id';

    /**
     * @var int
     */
    private $level = 1;

    /**
     * @var array
     */
    private $children = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Set has children
     *
     * @param bool $hasChildren
     * @return $this
     */
    public function setHasChildren($hasChildren)
    {
        $this->hasChildren = $hasChildren;
        return $this;
    }

    /**
     * Check if has children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return $this->hasChildren;
    }

    /**
     * Set ID field
     *
     * @param string $idField
     * @return $this
     */
    public function setIdField($idField)
    {
        $this->idField = $idField;
        return $this;
    }

    /**
     * Get ID field
     *
     * @return string
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * Set level
     *
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set children
     *
     * @param array $children
     * @return $this
     */
    public function setChildren($children)
    {
        $this->children = $children;
        return $this;
    }

    /**
     * Get children
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }
}
