<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Extended TestHelper for DataObject
 * Provides implementation for DataObject with additional test methods beyond the base DataObjectTestHelper
 */
class DataObjectTestHelperExtended extends DataObject
{
    /** @var mixed */
    private $itemIsQtyDecimal = null;
    /** @var mixed */
    private $hasQtyOptionUpdate = null;
    /** @var mixed */
    private $origQty = null;
    /** @var mixed */
    private $message = null;
    /** @var mixed */
    private $itemBackorders = null;
    /** @var mixed */
    private $itemQty = null;
    /** @var array */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid complex dependencies
    }

    /**
     * Get item is qty decimal
     *
     * @return mixed
     */
    public function getItemIsQtyDecimal()
    {
        return $this->itemIsQtyDecimal;
    }

    /**
     * Set item is qty decimal
     *
     * @param mixed $value
     * @return $this
     */
    public function setItemIsQtyDecimal($value)
    {
        $this->itemIsQtyDecimal = $value;
        return $this;
    }

    /**
     * Get has qty option update
     *
     * @return mixed
     */
    public function getHasQtyOptionUpdate()
    {
        return $this->hasQtyOptionUpdate;
    }

    /**
     * Set has qty option update
     *
     * @param mixed $value
     * @return $this
     */
    public function setHasQtyOptionUpdate($value)
    {
        $this->hasQtyOptionUpdate = $value;
        return $this;
    }

    /**
     * Get orig qty
     *
     * @return mixed
     */
    public function getOrigQty()
    {
        return $this->origQty;
    }

    /**
     * Set orig qty
     *
     * @param mixed $value
     * @return $this
     */
    public function setOrigQty($value)
    {
        $this->origQty = $value;
        return $this;
    }

    /**
     * Get message
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set message
     *
     * @param mixed $value
     * @return $this
     */
    public function setMessage($value)
    {
        $this->message = $value;
        return $this;
    }

    /**
     * Get item backorders
     *
     * @return mixed
     */
    public function getItemBackorders()
    {
        return $this->itemBackorders;
    }

    /**
     * Set item backorders
     *
     * @param mixed $value
     * @return $this
     */
    public function setItemBackorders($value)
    {
        $this->itemBackorders = $value;
        return $this;
    }

    /**
     * Get item qty
     *
     * @return mixed
     */
    public function getItemQty()
    {
        return $this->itemQty;
    }

    /**
     * Set item qty
     *
     * @param mixed $value
     * @return $this
     */
    public function setItemQty($value)
    {
        $this->itemQty = $value;
        return $this;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
}
