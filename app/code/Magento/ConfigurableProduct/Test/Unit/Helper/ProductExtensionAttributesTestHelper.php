<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ConfigurableProduct\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductExtensionInterface;

/**
 * Test helper for ProductExtensionInterface mocking
 *
 * This helper provides setter and getter methods to simulate
 * extension attributes for different product types (configurable, bundle,
 * downloadable, gift card, etc.) in unit tests.
 */
class ProductExtensionAttributesTestHelper implements ProductExtensionInterface
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     *
     * Skips parent constructor to avoid dependency requirements.
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get Configurable Product Options
     *
     * @return mixed|null
     */
    public function getConfigurableProductOptions()
    {
        return $this->data['configurable_product_options'] ?? null;
    }

    /**
     * Set Configurable Product Options
     *
     * @param mixed $options
     * @return $this
     */
    public function setConfigurableProductOptions($options)
    {
        $this->data['configurable_product_options'] = $options;
        return $this;
    }

    /**
     * Get Configurable Product Links
     *
     * @return mixed|null
     */
    public function getConfigurableProductLinks()
    {
        return $this->data['configurable_product_links'] ?? null;
    }

    /**
     * Set Configurable Product Links
     *
     * @param mixed $links
     * @return $this
     */
    public function setConfigurableProductLinks($links)
    {
        $this->data['configurable_product_links'] = $links;
        return $this;
    }

    /**
     * Get Bundle Options
     *
     * @return mixed|null
     */
    public function getBundleOptions()
    {
        return $this->data['bundle_options'] ?? null;
    }

    /**
     * Get Downloadable Product Links
     *
     * @return mixed|null
     */
    public function getDownloadableProductLinks()
    {
        return $this->data['downloadable_product_links'] ?? null;
    }

    /**
     * Get Downloadable Product Samples
     *
     * @return mixed|null
     */
    public function getDownloadableProductSamples()
    {
        return $this->data['downloadable_product_samples'] ?? null;
    }

    /**
     * Set Bundle Options
     *
     * @param mixed $bundleOptions
     * @return $this
     */
    public function setBundleOptions($bundleOptions)
    {
        $this->data['bundle_options'] = $bundleOptions;
        return $this;
    }

    /**
     * Set Downloadable Product Links
     *
     * @param mixed $downloadableProductLinks
     * @return $this
     */
    public function setDownloadableProductLinks($downloadableProductLinks)
    {
        $this->data['downloadable_product_links'] = $downloadableProductLinks;
        return $this;
    }

    /**
     * Set Downloadable Product Samples
     *
     * @param mixed $downloadableProductSamples
     * @return $this
     */
    public function setDownloadableProductSamples($downloadableProductSamples)
    {
        $this->data['downloadable_product_samples'] = $downloadableProductSamples;
        return $this;
    }

    /**
     * Get Gift Card Amounts
     *
     * @return mixed|null
     */
    public function getGiftcardAmounts()
    {
        return $this->data['giftcard_amounts'] ?? null;
    }

    /**
     * Set Gift Card Amounts
     *
     * @param mixed $giftcardAmounts
     * @return $this
     */
    public function setGiftcardAmounts($giftcardAmounts)
    {
        $this->data['giftcard_amounts'] = $giftcardAmounts;
        return $this;
    }

    /**
     * Get Custom Options
     *
     * @return mixed|null
     */
    public function getCustomOptions()
    {
        return $this->data['custom_options'] ?? null;
    }

    /**
     * Set Custom Options
     *
     * @param mixed $customOptions
     * @return $this
     */
    public function setCustomOptions($customOptions)
    {
        $this->data['custom_options'] = $customOptions;
        return $this;
    }

    /**
     * Get Stock Item
     *
     * @return mixed|null
     */
    public function getStockItem()
    {
        return $this->data['stock_item'] ?? null;
    }

    /**
     * Set Stock Item
     *
     * @param mixed $stockItem
     * @return $this
     */
    public function setStockItem($stockItem)
    {
        $this->data['stock_item'] = $stockItem;
        return $this;
    }

    /**
     * Get Category Links
     *
     * @return array
     */
    public function getCategoryLinks(): array
    {
        return $this->data['category_links'] ?? [];
    }

    /**
     * Set Category Links
     *
     * @param array $categoryLinks
     * @return $this
     */
    public function setCategoryLinks($categoryLinks)
    {
        $this->data['category_links'] = $categoryLinks;
        return $this;
    }

    /**
     * Get Website IDs
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->data['website_ids'] ?? [];
    }

    /**
     * Set Website IDs
     *
     * @param array $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds)
    {
        $this->data['website_ids'] = $websiteIds;
        return $this;
    }

    /**
     * Get Bundle Product Options
     *
     * @return mixed|null
     */
    public function getBundleProductOptions()
    {
        return $this->data['bundle_product_options'] ?? null;
    }

    /**
     * Set Bundle Product Options
     *
     * @param mixed $bundleProductOptions
     * @return $this
     */
    public function setBundleProductOptions($bundleProductOptions)
    {
        $this->data['bundle_product_options'] = $bundleProductOptions;
        return $this;
    }

    /**
     * Get Discounts
     *
     * @return mixed|null
     */
    public function getDiscounts()
    {
        return $this->data['discounts'] ?? null;
    }

    /**
     * Set Discounts
     *
     * @param mixed $discounts
     * @return $this
     */
    public function setDiscounts($discounts)
    {
        $this->data['discounts'] = $discounts;
        return $this;
    }

    /**
     * Get Vertex Tax Calculation Messages
     *
     * @return mixed|null
     */
    public function getVertexTaxCalculationMessages()
    {
        return $this->data['vertex_tax_calculation_messages'] ?? null;
    }
    /**
     * Set Vertex Tax Calculation Messages
     *
     * @param mixed $vertexTaxCalculationMessages
     * @return $this
     */
    public function setVertexTaxCalculationMessages($vertexTaxCalculationMessages)
    {
        $this->data['vertex_tax_calculation_messages'] = $vertexTaxCalculationMessages;
        return $this;
    }
}
