<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\Helper;

use Magento\Wishlist\Model\Item\Option;

/**
 * Test helper for Option class
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class OptionTestHelper extends Option
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var bool
     */
    private $deleted = false;

    /**
     * @var mixed
     */
    private $item;

    /**
     * @var int
     */
    private $deleteCount = 0;

    /**
     * @var int
     */
    private $saveCount = 0;

    /**
     * Constructor
     *
     * @param string|null $code
     * @param mixed $value
     * @param bool $deleted
     */
    public function __construct($code = null, $value = null, $deleted = false)
    {
        $this->code = $code;
        $this->value = $value;
        $this->deleted = $deleted;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set data
     *
     * @param mixed $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        return $this;
    }

    /**
     * Set item
     *
     * @param mixed $item
     * @return $this
     */
    public function setItem($item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Get item
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Set value
     *
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Set deleted
     *
     * @param bool $deleted
     * @return $this
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * Get deleted
     *
     * @return bool
     */
    public function isDeleted($isDeleted = null)
    {
        return $this->deleted;
    }

    /**
     * Save
     *
     * @return $this
     */
    public function save()
    {
        $this->saveCount++;
        return $this;
    }

    /**
     * Delete
     *
     * @return $this
     */
    public function delete()
    {
        $this->deleteCount++;
        return $this;
    }

    /**
     * Load
     *
     * @param mixed $id
     * @param mixed $field
     * @return $this
     */
    public function load($id, $field = null)
    {
        return $this;
    }

    /**
     * Get delete count
     *
     * @return int
     */
    public function getDeleteCount()
    {
        return $this->deleteCount;
    }

    /**
     * Get save count
     *
     * @return int
     */
    public function getSaveCount()
    {
        return $this->saveCount;
    }
}
