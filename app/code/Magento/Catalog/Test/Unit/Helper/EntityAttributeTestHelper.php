<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Entity\Attribute;

class EntityAttributeTestHelper extends Attribute
{
    /**
     * @var mixed
     */
    private $groupSortPath = null;

    /**
     * @var mixed
     */
    private $sortPath = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getGroupSortPath()
    {
        return $this->groupSortPath;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setGroupSortPath($value)
    {
        $this->groupSortPath = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSortPath()
    {
        return $this->sortPath;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setSortPath($value)
    {
        $this->sortPath = $value;
        return $this;
    }
}

