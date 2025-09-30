<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Bundle\Test\Unit\Helper;

use Magento\Bundle\Api\Data\BundleOptionInterface;

/**
 * Test helper for Magento\Bundle\Api\Data\BundleOptionInterface
 *
 * Implements the BundleOptionInterface to add custom methods for testing
 */
class BundleOptionInterfaceTestHelper implements BundleOptionInterface
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
     * Custom getProductLinks method for testing
     *
     * @return mixed
     */
    public function getProductLinks()
    {
        return $this->data['product_links'] ?? [];
    }

    /**
     * Custom setProductLinks method for testing
     *
     * @param mixed $productLinks
     * @return self
     */
    public function setProductLinks($productLinks): self
    {
        $this->data['product_links'] = $productLinks;
        return $this;
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
    public function getOptionQty()
    {
        return $this->data['option_qty'] ?? 0;
    }

    /**
     * @inheritdoc
     */
    public function setOptionQty($optionQty)
    {
        $this->data['option_qty'] = $optionQty;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getOptionSelections()
    {
        return $this->data['option_selections'] ?? [];
    }

    /**
     * @inheritdoc
     */
    public function setOptionSelections(array $optionSelections)
    {
        $this->data['option_selections'] = $optionSelections;
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
    public function setExtensionAttributes($extensionAttributes)
    {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }
}
