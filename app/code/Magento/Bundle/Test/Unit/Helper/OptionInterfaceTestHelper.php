<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Api\Data\OptionInterface;

/**
 * Test helper for Magento\Bundle\Api\Data\OptionInterface
 * 
 * Implements the OptionInterface to add custom methods for testing
 */
class OptionInterfaceTestHelper implements OptionInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // No dependencies needed
    }

    /**
     * Custom setDefaultTitle method for testing
     *
     * @param mixed $title
     * @return self
     */
    public function setDefaultTitle($title): self
    {
        $this->data['default_title'] = $title;
        return $this;
    }

    /**
     * Custom getDefaultTitle method for testing
     *
     * @return mixed
     */
    public function getDefaultTitle()
    {
        return $this->data['default_title'] ?? null;
    }

    /**
     * Set test data for flexible state management
     *
     * @param string $key
     * @param mixed $value
     * @return self
     */
    public function setTestData(string $key, $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * Get test data
     *
     * @param string $key
     * @return mixed
     */
    public function getTestData(string $key)
    {
        return $this->data[$key] ?? null;
    }

    // Required interface methods
    
    /**
     * @inheritdoc
     */
    public function getOptionId()
    {
        return $this->data['option_id'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setOptionId($optionId)
    {
        $this->data['option_id'] = $optionId;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return $this->data['title'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRequired()
    {
        return $this->data['required'] ?? false;
    }

    /**
     * @inheritdoc
     */
    public function setRequired($required)
    {
        $this->data['required'] = $required;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->data['type'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setType($type)
    {
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPosition()
    {
        return $this->data['position'] ?? 0;
    }

    /**
     * @inheritdoc
     */
    public function setPosition($position)
    {
        $this->data['position'] = $position;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSku()
    {
        return $this->data['sku'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setSku($sku)
    {
        $this->data['sku'] = $sku;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProductLinks()
    {
        return $this->data['product_links'] ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setProductLinks(?array $productLinks = null)
    {
        $this->data['product_links'] = $productLinks;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->data['extension_attributes'] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(\Magento\Bundle\Api\Data\OptionExtensionInterface $extensionAttributes)
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }
}
