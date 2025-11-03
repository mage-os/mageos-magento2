<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Eav\Test\Unit\Helper;

use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;

class AbstractBackendTestHelper extends AbstractBackend
{
    /**
     * @var mixed
     */
    private $backend = null;

    /**
     * @var mixed
     */
    private $table = null;

    /**
     * @var mixed
     */
    private $attribute = null;

    /**
     * @var mixed
     */
    private $attributeCode = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getBackend()
    {
        return $this->backend;
    }

    /**
     * @param mixed $backend
     * @return $this
     */
    public function setBackend($backend)
    {
        $this->backend = $backend;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * @param mixed $table
     * @return $this
     */
    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * @param mixed $attribute
     * @return $this
     */
    public function setAttributeReturn($attribute)
    {
        $this->attribute = $attribute;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     * @param mixed $code
     * @return $this
     */
    public function setAttributeCodeReturn($code)
    {
        $this->attributeCode = $code;
        return $this;
    }
}

