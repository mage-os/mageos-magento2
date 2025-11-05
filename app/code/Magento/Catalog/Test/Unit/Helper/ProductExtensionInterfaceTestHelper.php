<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductExtensionInterface;

/**
 * Test helper for ProductExtensionInterface
 *
 * Since ProductExtensionInterface is a generated interface, we cannot use createMock()
 * or createPartialMock() for methods that don't exist in the base interface.
 * This helper provides a minimal implementation for testing purposes.
 */
class ProductExtensionInterfaceTestHelper implements ProductExtensionInterface
{
    /** @var mixed */
    private $stockItem = null;
    /** @var mixed */
    private $bundleProductOptions = null;
    /** @var mixed */
    private $categoryLinks = null;
    /** @var mixed */
    private $configurableProductLinks = null;
    /** @var mixed */
    private $configurableProductOptions = null;
    /** @var mixed */
    private $discounts = null;
    /** @var mixed */
    private $downloadableProductLinks = null;
    /** @var mixed */
    private $downloadableProductSamples = null;
    /** @var mixed */
    private $websiteIds = null;
    /** @var mixed */
    private $giftcardAmounts = null;
    /** @var mixed */
    private $testStockItem = null;
    /** @var mixed */
    private $testStockItemQty = null;

    public function __construct()
    {
    }

    public function __toArray(): array
    {
        return [];
    }

    public function getStockItem()
    {
        return $this->stockItem;
    }

    public function setStockItem($stockItem)
    {
        $this->stockItem = $stockItem;
        return $this;
    }

    public function getBundleProductOptions()
    {
        return $this->bundleProductOptions;
    }

    public function setBundleProductOptions($bundleProductOptions)
    {
        $this->bundleProductOptions = $bundleProductOptions;
        return $this;
    }

    public function getCategoryLinks()
    {
        return $this->categoryLinks;
    }

    public function setCategoryLinks($categoryLinks)
    {
        $this->categoryLinks = $categoryLinks;
        return $this;
    }

    public function getConfigurableProductLinks()
    {
        return $this->configurableProductLinks;
    }

    public function setConfigurableProductLinks($configurableProductLinks)
    {
        $this->configurableProductLinks = $configurableProductLinks;
        return $this;
    }

    public function getConfigurableProductOptions()
    {
        return $this->configurableProductOptions;
    }

    public function setConfigurableProductOptions($configurableProductOptions)
    {
        $this->configurableProductOptions = $configurableProductOptions;
        return $this;
    }

    public function getDiscounts()
    {
        return $this->discounts;
    }

    public function setDiscounts($discounts)
    {
        $this->discounts = $discounts;
        return $this;
    }

    public function getDownloadableProductLinks()
    {
        return $this->downloadableProductLinks;
    }

    public function setDownloadableProductLinks($downloadableProductLinks)
    {
        $this->downloadableProductLinks = $downloadableProductLinks;
        return $this;
    }

    public function getDownloadableProductSamples()
    {
        return $this->downloadableProductSamples;
    }

    public function setDownloadableProductSamples($downloadableProductSamples)
    {
        $this->downloadableProductSamples = $downloadableProductSamples;
        return $this;
    }

    public function getWebsiteIds()
    {
        return $this->websiteIds;
    }

    public function setWebsiteIds($websiteIds)
    {
        $this->websiteIds = $websiteIds;
        return $this;
    }

    public function getGiftcardAmounts()
    {
        return $this->giftcardAmounts;
    }

    public function setGiftcardAmounts($giftcardAmounts)
    {
        $this->giftcardAmounts = $giftcardAmounts;
        return $this;
    }

    public function getTestStockItem()
    {
        return $this->testStockItem;
    }

    public function setTestStockItem($testStockItem)
    {
        $this->testStockItem = $testStockItem;
        return $this;
    }

    public function getTestStockItemQty()
    {
        return $this->testStockItemQty;
    }

    public function setTestStockItemQty($testStockItemQty)
    {
        $this->testStockItemQty = $testStockItemQty;
        return $this;
    }
}
