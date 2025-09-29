<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Model\Product\Type;

/**
 * Test helper for Bundle Product Type
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductTypeTestHelper extends Type
{
    /**
     * @var mixed
     */
    private $typeId = null;

    /**
     * @var mixed
     */
    private $storeId = null;

    /**
     * @var mixed
     */
    private $typeInstance = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues
    }

    /**
     * Get type ID
     *
     * @return mixed
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set type ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setTypeId($id): self
    {
        $this->typeId = $id;
        return $this;
    }

    /**
     * Get store ID
     *
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set store ID
     *
     * @param mixed $id
     * @return $this
     */
    public function setStoreId($id): self
    {
        $this->storeId = $id;
        return $this;
    }

    /**
     * Get type instance
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        return $this->typeInstance;
    }

    /**
     * Set type instance
     *
     * @param mixed $instance
     * @return $this
     */
    public function setTypeInstance($instance): self
    {
        $this->typeInstance = $instance;
        return $this;
    }
}
