<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;

class ProductAttributeTestHelper extends Attribute
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var bool
     */
    private $isScopeGlobal = false;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->isScopeGlobal;
    }

    /**
     * @param bool $isScopeGlobal
     * @return $this
     */
    public function setIsScopeGlobal($isScopeGlobal)
    {
        $this->isScopeGlobal = $isScopeGlobal;
        return $this;
    }
}

