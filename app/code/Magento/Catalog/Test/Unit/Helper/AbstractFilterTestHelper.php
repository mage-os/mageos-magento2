<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Layer\Filter\AbstractFilter;

/**
 * Test helper for AbstractFilter class
 */
class AbstractFilterTestHelper extends AbstractFilter
{
    /**
     * @var mixed
     */
    private $attributeModel;

    /**
     * Skip parent constructor
     */
    public function __construct()
    {
    }

    /**
     * Get attribute model
     *
     * @return mixed
     */
    public function getAttributeModel()
    {
        return $this->attributeModel;
    }

    /**
     * Set attribute model
     *
     * @param mixed $attr
     * @return void
     */
    public function setAttributeModel($attr)
    {
        $this->attributeModel = $attr;
    }

    /**
     * Has attribute model
     *
     * @return bool
     */
    public function hasAttributeModel()
    {
        return $this->attributeModel !== null;
    }
}
