<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Data\Test\Unit\Helper;

use Magento\Framework\DataObject;

class DataObjectTestHelper extends DataObject
{
    /**
     * @var mixed
     */
    private $isDuplicate = null;

    /**
     * @var mixed
     */
    private $lockedAttribute = null;

    /**
     * @var mixed
     */
    private $mediaAttributes = null;

    public function __construct()
    {
        // Empty constructor
    }

    /**
     * @return mixed
     */
    public function getIsDuplicate()
    {
        return $this->isDuplicate;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setIsDuplicate($value)
    {
        $this->isDuplicate = $value;
        return $this;
    }

    /**
     * @param mixed $attribute
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isLockedAttribute($attribute)
    {
        return $this->lockedAttribute;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setLockedAttribute($value)
    {
        $this->lockedAttribute = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMediaAttributes()
    {
        return $this->mediaAttributes;
    }

    /**
     * @param mixed $attributes
     * @return $this
     */
    public function setMediaAttributes($attributes)
    {
        $this->mediaAttributes = $attributes;
        return $this;
    }
}

