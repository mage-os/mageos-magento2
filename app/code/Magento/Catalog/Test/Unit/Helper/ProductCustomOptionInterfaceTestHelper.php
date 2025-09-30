<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

/**
 * Test helper for Magento\Catalog\Api\Data\ProductCustomOptionInterface
 * 
 * Implements ProductCustomOptionInterface for testing with custom methods
 */
class ProductCustomOptionInterfaceTestHelper implements ProductCustomOptionInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Set product for testing
     *
     * @param mixed $product
     * @return self
     */
    public function setProduct($product): self
    {
        $this->data['product'] = $product;
        return $this;
    }

    /**
     * Get product for testing
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductSku()
    {
        return $this->data['product_sku'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setProductSku($productSku)
    {
        $this->data['product_sku'] = $productSku;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptionId()
    {
        return $this->data['option_id'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setOptionId($optionId)
    {
        $this->data['option_id'] = $optionId;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->data['title'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->data['type'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->data['sort_order'] ?? 0;
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($sortOrder)
    {
        $this->data['sort_order'] = $sortOrder;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsRequire()
    {
        return $this->data['is_require'] ?? false;
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRequire($isRequired)
    {
        $this->data['is_require'] = $isRequired;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return $this->data['price'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriceType()
    {
        return $this->data['price_type'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPriceType($priceType)
    {
        $this->data['price_type'] = $priceType;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->data['sku'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($sku)
    {
        $this->data['sku'] = $sku;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileExtension()
    {
        return $this->data['file_extension'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setFileExtension($fileExtension)
    {
        $this->data['file_extension'] = $fileExtension;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxCharacters()
    {
        return $this->data['max_characters'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxCharacters($maxCharacters)
    {
        $this->data['max_characters'] = $maxCharacters;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageSizeX()
    {
        return $this->data['image_size_x'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setImageSizeX($imageSizeX)
    {
        $this->data['image_size_x'] = $imageSizeX;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getImageSizeY()
    {
        return $this->data['image_size_y'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setImageSizeY($imageSizeY)
    {
        $this->data['image_size_y'] = $imageSizeY;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->data['values'] ?? [];
    }

    /**
     * {@inheritdoc}
     */
    public function setValues(?array $values = null)
    {
        $this->data['values'] = $values;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->data['extension_attributes'] ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Magento\Catalog\Api\Data\ProductCustomOptionExtensionInterface $extensionAttributes
    ) {
        $this->data['extension_attributes'] = $extensionAttributes;
        return $this;
    }
}
