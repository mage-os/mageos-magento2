<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Category\Tree;

class TreeTestHelper extends Tree
{
    /**
     * @var mixed
     */
    private $nodes;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @param mixed $nodeId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function loadNode($nodeId)
    {
        return $this;
    }

    /**
     * @return mixed
     */
    public function loadChildren()
    {
        return $this->nodes;
    }

    /**
     * @param mixed $nodes
     * @return $this
     */
    public function setNodes($nodes)
    {
        $this->nodes = $nodes;
        return $this;
    }
}

