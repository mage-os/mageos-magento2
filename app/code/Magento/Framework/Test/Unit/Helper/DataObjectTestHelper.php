<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * TestHelper for DataObject
 * Provides implementation for DataObject with additional test methods
 */
class DataObjectTestHelper extends DataObject
{
    /** @var mixed */
    private $result = null;
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
     * Get result
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }

    /**
     * Set result
     *
     * @param mixed $result
     * @return $this
     */
    public function setResult($result)
    {
        $this->result = $result;
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
        return isset($this->data[$key]) ? $this->data[$key] : null;
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
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Add data
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr)
    {
        foreach ($arr as $key => $value) {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Unset data
     *
     * @param string|null $key
     * @return $this
     */
    public function unsetData($key = null)
    {
        if ($key === null) {
            $this->data = [];
        } else {
            unset($this->data[$key]);
        }
        return $this;
    }

    /**
     * Has data
     *
     * @param string $key
     * @return bool
     */
    public function hasData($key = '')
    {
        return isset($this->data[$key]);
    }

    /**
     * To array
     *
     * @param array $arrAttributes
     * @return array
     */
    public function toArray($arrAttributes = [])
    {
        return $this->data;
    }

    /**
     * To JSON
     *
     * @param array $arrAttributes
     * @return string
     */
    public function toJson($arrAttributes = [])
    {
        return json_encode($this->data);
    }

    /**
     * To string
     *
     * @param string $format
     * @return string
     */
    public function toString($format = '')
    {
        return json_encode($this->data);
    }

    /**
     * Check if empty
     *
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->data);
    }

    /**
     * Get item is qty decimal
     *
     * @return bool|null
     */
    public function getItemIsQtyDecimal()
    {
        return $this->data['item_is_qty_decimal'] ?? null;
    }

    /**
     * Set item is qty decimal
     *
     * @param bool|null $itemIsQtyDecimal
     * @return $this
     */
    public function setItemIsQtyDecimal($itemIsQtyDecimal)
    {
        $this->data['item_is_qty_decimal'] = $itemIsQtyDecimal;
        return $this;
    }

    /**
     * Get has qty option update
     *
     * @return bool|null
     */
    public function getHasQtyOptionUpdate()
    {
        return $this->data['has_qty_option_update'] ?? null;
    }

    /**
     * Set has qty option update
     *
     * @param bool|null $hasQtyOptionUpdate
     * @return $this
     */
    public function setHasQtyOptionUpdate($hasQtyOptionUpdate)
    {
        $this->data['has_qty_option_update'] = $hasQtyOptionUpdate;
        return $this;
    }

    /**
     * Get item use old qty
     *
     * @return mixed
     */
    public function getItemUseOldQty()
    {
        return $this->data['item_use_old_qty'] ?? null;
    }

    /**
     * Set item use old qty
     *
     * @param mixed $itemUseOldQty
     * @return $this
     */
    public function setItemUseOldQty($itemUseOldQty)
    {
        $this->data['item_use_old_qty'] = $itemUseOldQty;
        return $this;
    }

    /**
     * Get item backorders
     *
     * @return int|null
     */
    public function getItemBackorders()
    {
        return $this->data['item_backorders'] ?? null;
    }

    /**
     * Set item backorders
     *
     * @param int|null $itemBackorders
     * @return $this
     */
    public function setItemBackorders($itemBackorders)
    {
        $this->data['item_backorders'] = $itemBackorders;
        return $this;
    }
}
