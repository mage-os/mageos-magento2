<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * TestHelper for Product
 * Provides implementation for Product with additional test methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
 */
class ProductTestHelper extends Product
{
    /** @var bool */
    private $isChangedWebsites = false;
    /** @var int|null */
    private $id = null;
    /** @var string|null */
    private $name = null;
    /** @var int|null */
    private $storeId = null;
    /** @var string|null */
    private $typeId = null;
    /** @var bool|null */
    private $statusChanged = null;
    /** @var bool|null */
    private $isSalable = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Get is changed websites
     *
     * @return bool
     */
    public function getIsChangedWebsites()
    {
        return $this->isChangedWebsites;
    }

    /**
     * Set is changed websites
     *
     * @param bool $value
     * @return $this
     */
    public function setIsChangedWebsites($value)
    {
        $this->isChangedWebsites = $value;
        return $this;
    }

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param int|null $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string|null $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get store id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->storeId;
    }

    /**
     * Set store id
     *
     * @param int|null $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    /**
     * Get type id
     *
     * @return string|null
     */
    public function getTypeId()
    {
        return $this->typeId;
    }

    /**
     * Set type id
     *
     * @param string|null $typeId
     * @return $this
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
        return $this;
    }

    /**
     * Get status changed
     *
     * @return bool|null
     */
    public function getStatusChanged()
    {
        return $this->statusChanged;
    }

    /**
     * Set status changed
     *
     * @param bool|null $statusChanged
     * @return $this
     */
    public function setStatusChanged($statusChanged)
    {
        $this->statusChanged = $statusChanged;
        return $this;
    }

    /**
     * Data has changed for
     *
     * @param string $field
     * @return bool
     */
    public function dataHasChangedFor($field)
    {
        if ($field === 'status') {
            return $this->statusChanged;
        }
        return false;
    }

    /**
     * Wakeup method
     */
    public function __wakeup()
    {
        // Required method implementation
    }

    /**
     * Get is salable
     *
     * @return bool|null
     */
    public function getIsSalable()
    {
        return $this->isSalable;
    }

    /**
     * Set is salable
     *
     * @param bool|null $isSalable
     * @return $this
     */
    public function setIsSalable($isSalable)
    {
        $this->isSalable = $isSalable;
        return $this;
    }
}
