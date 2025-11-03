<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\EntityManager\Test\Unit\Helper;

use Magento\Framework\EntityManager\MetadataPool;

/**
 * TestHelper for MetadataPool
 * Provides implementation for MetadataPool with additional test methods
 *
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class MetadataPoolTestHelper extends MetadataPool
{
    /** @var string|null */
    private $linkField = null;
    /** @var mixed */
    protected $metadata = null;
    /** @var array */
    private $data = [];

    /**
     * Constructor - skip parent to avoid dependencies
     */
    public function __construct()
    {
        // Don't call parent constructor to avoid dependencies
    }

    /**
     * Get link field
     *
     * @return string|null
     */
    public function getLinkField()
    {
        return $this->linkField;
    }

    /**
     * Set link field
     *
     * @param string|null $linkField
     * @return $this
     */
    public function setLinkField($linkField)
    {
        $this->linkField = $linkField;
        return $this;
    }

    /**
     * Get metadata
     *
     * @param mixed $entityType
     * @return mixed
     */
    public function getMetadata($entityType)
    {
        return $this->metadata;
    }

    /**
     * Set metadata
     *
     * @param mixed $metadata
     * @return $this
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Get data
     *
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }
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
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Has data
     *
     * @param string $key
     * @return bool
     */
    public function hasData($key = null)
    {
        if ($key === null) {
            return !empty($this->data);
        }
        return isset($this->data[$key]);
    }
}
