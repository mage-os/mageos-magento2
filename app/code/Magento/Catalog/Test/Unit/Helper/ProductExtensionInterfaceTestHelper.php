<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductExtensionInterface;

/**
 * TestHelper for ProductExtensionInterface
 * Provides implementation for ProductExtensionInterface with additional test methods
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ProductExtensionInterfaceTestHelper implements ProductExtensionInterface
{
    /** @var array */
    private $data = [];
    /** @var mixed */
    private $stockItem = null;
    /** @var array */
    private $websiteIds = [];
    /** @var mixed */
    private $categoryLinks = null;
    /** @var mixed */
    private $configurableProductOptions = null;
    /** @var mixed */
    private $configurableProductLinks = null;
    /** @var mixed */
    private $downloadableProductLinks = null;
    /** @var mixed */
    private $downloadableProductSamples = null;
    /** @var mixed */
    private $giftcardAmounts = null;
    /** @var mixed */
    private $bundleProductOptions = null;
    /** @var mixed */
    private $giftWrapping = null;
    /** @var mixed */
    private $discounts = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Mock implementation
    }

    /**
     * Set stock item
     *
     * @param mixed $stockItem
     * @return $this
     */
    public function setStockItem($stockItem)
    {
        $this->stockItem = $stockItem;
        return $this;
    }

    /**
     * Get stock items
     *
     * @return mixed
     */
    public function getStockItems()
    {
        return $this->data['stock_items'] ?? null;
    }

    /**
     * Set stock items
     *
     * @param mixed $stockItems
     * @return $this
     */
    public function setStockItems($stockItems)
    {
        $this->data['stock_items'] = $stockItems;
        return $this;
    }

    /**
     * Get stock item
     *
     * @return mixed
     */
    public function getStockItem()
    {
        return $this->stockItem;
    }

    /**
     * Get website ids
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        return $this->websiteIds;
    }

    /**
     * Set website ids
     *
     * @param array $websiteIds
     * @return $this
     */
    public function setWebsiteIds($websiteIds)
    {
        $this->websiteIds = $websiteIds;
        return $this;
    }

    /**
     * Get category links
     *
     * @return mixed
     */
    public function getCategoryLinks()
    {
        return $this->categoryLinks;
    }

    /**
     * Set category links
     *
     * @param mixed $categoryLinks
     * @return $this
     */
    public function setCategoryLinks($categoryLinks)
    {
        $this->categoryLinks = $categoryLinks;
        return $this;
    }

    /**
     * Get configurable product options
     *
     * @return mixed
     */
    public function getConfigurableProductOptions()
    {
        return $this->configurableProductOptions;
    }

    /**
     * Set configurable product options
     *
     * @param mixed $configurableProductOptions
     * @return $this
     */
    public function setConfigurableProductOptions($configurableProductOptions)
    {
        $this->configurableProductOptions = $configurableProductOptions;
        return $this;
    }

    /**
     * Get configurable product links
     *
     * @return mixed
     */
    public function getConfigurableProductLinks()
    {
        return $this->configurableProductLinks;
    }

    /**
     * Set configurable product links
     *
     * @param mixed $configurableProductLinks
     * @return $this
     */
    public function setConfigurableProductLinks($configurableProductLinks)
    {
        $this->configurableProductLinks = $configurableProductLinks;
        return $this;
    }

    /**
     * Get downloadable product links
     *
     * @return mixed
     */
    public function getDownloadableProductLinks()
    {
        return $this->downloadableProductLinks;
    }

    /**
     * Set downloadable product links
     *
     * @param mixed $downloadableProductLinks
     * @return $this
     */
    public function setDownloadableProductLinks($downloadableProductLinks)
    {
        $this->downloadableProductLinks = $downloadableProductLinks;
        return $this;
    }

    /**
     * Get downloadable product samples
     *
     * @return mixed
     */
    public function getDownloadableProductSamples()
    {
        return $this->downloadableProductSamples;
    }

    /**
     * Set downloadable product samples
     *
     * @param mixed $downloadableProductSamples
     * @return $this
     */
    public function setDownloadableProductSamples($downloadableProductSamples)
    {
        $this->downloadableProductSamples = $downloadableProductSamples;
        return $this;
    }

    /**
     * Get giftcard amounts
     *
     * @return mixed
     */
    public function getGiftcardAmounts()
    {
        return $this->giftcardAmounts;
    }

    /**
     * Set giftcard amounts
     *
     * @param mixed $giftcardAmounts
     * @return $this
     */
    public function setGiftcardAmounts($giftcardAmounts)
    {
        $this->giftcardAmounts = $giftcardAmounts;
        return $this;
    }

    /**
     * Get bundle product options
     *
     * @return mixed
     */
    public function getBundleProductOptions()
    {
        return $this->bundleProductOptions;
    }

    /**
     * Set bundle product options
     *
     * @param mixed $bundleProductOptions
     * @return $this
     */
    public function setBundleProductOptions($bundleProductOptions)
    {
        $this->bundleProductOptions = $bundleProductOptions;
        return $this;
    }

    /**
     * Get gift wrapping
     *
     * @return mixed
     */
    public function getGiftWrapping()
    {
        return $this->giftWrapping;
    }

    /**
     * Set gift wrapping
     *
     * @param mixed $giftWrapping
     * @return $this
     */
    public function setGiftWrapping($giftWrapping)
    {
        $this->giftWrapping = $giftWrapping;
        return $this;
    }

    /**
     * Get extension attributes
     *
     * @return \Magento\Catalog\Api\Data\ProductExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this;
    }

    /**
     * Set extension attributes
     *
     * @param \Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Magento\Catalog\Api\Data\ProductExtensionInterface $extensionAttributes)
    {
        return $this;
    }

    /**
     * Get discounts
     *
     * @return mixed
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * Set discounts
     *
     * @param mixed $discounts
     * @return $this
     */
    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
        return $this;
    }

    /**
     * Get test stock item
     *
     * @return mixed
     */
    public function getTestStockItem()
    {
        return $this->data['test_stock_item'] ?? null;
    }

    /**
     * Set test stock item
     *
     * @param mixed $testStockItem
     * @return $this
     */
    public function setTestStockItem($testStockItem)
    {
        $this->data['test_stock_item'] = $testStockItem;
        return $this;
    }

    /**
     * Get test stock item qty
     *
     * @return mixed
     */
    public function getTestStockItemQty()
    {
        return $this->data['test_stock_item_qty'] ?? null;
    }

    /**
     * Set test stock item qty
     *
     * @param mixed $testStockItemQty
     * @return $this
     */
    public function setTestStockItemQty($testStockItemQty)
    {
        $this->data['test_stock_item_qty'] = $testStockItemQty;
        return $this;
    }
}
