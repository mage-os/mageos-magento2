<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Model\ResourceModel\Eav\Attribute;
use Magento\Swatches\Model\Swatch;

/**
 * Test helper for Attribute class
 */
class AttributeTestHelper extends Attribute
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var string
     */
    private $additionalData;

    /**
     * Constructor
     *
     * @param string|null $additionalData
     */
    public function __construct($additionalData = null)
    {
        $this->additionalData = $additionalData;
    }

    /**
     * Has data
     *
     * @param string|null $key
     * @return bool
     */
    public function hasData($key = null)
    {
        return $key !== Swatch::SWATCH_INPUT_TYPE_KEY;
    }

    /**
     * Get data
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        if ($key === 'additional_data') {
            return $this->additionalData;
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

    /**
     * Get ID
     *
     * @return string
     */
    public function getId()
    {
        return 'id';
    }

    /**
     * Get frontend label
     *
     * @return string
     */
    public function getFrontendLabel()
    {
        return 'label';
    }

    /**
     * Get attribute code
     *
     * @return string
     */
    public function getAttributeCode()
    {
        return 'code';
    }

    /**
     * Get source
     *
     * @return object
     */
    public function getSource()
    {
        return new class {
            public function getAllOptions($withEmpty = false)
            {
                return ['options'];
            }
        };
    }
}
