<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Store\Test\Unit\Helper;

use Magento\Store\Api\Data\StoreInterface;

/**
 * Test helper for StoreInterface
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class StoreTestHelper implements StoreInterface
{
    /**
     * @var int
     */
    private int $storeId;

    /**
     * Constructor
     *
     * @param int $storeId
     */
    public function __construct(int $storeId = 1)
    {
        $this->storeId = $storeId;
    }

    public function getId() { return $this->storeId; }
    public function getCode() { return 'test_store_' . $this->storeId; }
    public function getName() { return 'Test Store ' . $this->storeId; }
    public function getWebsiteId() { return 1; }
    public function getStoreGroupId() { return 1; }
    public function getIsActive() { return true; }
    public function getSortOrder() { return 0; }
    public function getLocaleCode() { return 'en_US'; }
    public function getBaseCurrencyCode() { return 'USD'; }
    public function getDefaultDisplayCurrencyCode() { return 'USD'; }
    public function getTimezone() { return 'America/New_York'; }
    public function getWeightUnit() { return 'lbs'; }
    public function getBaseUrl($type = null) { return 'https://example.com/'; }
    public function getBaseLinkUrl() { return 'https://example.com/'; }
    public function getBaseStaticUrl() { return 'https://example.com/static/'; }
    public function getBaseMediaUrl() { return 'https://example.com/media/'; }
    public function getSecureBaseUrl($type = null) { return 'https://example.com/'; }
    public function getSecureBaseLinkUrl() { return 'https://example.com/'; }
    public function getSecureBaseStaticUrl() { return 'https://example.com/static/'; }
    public function getSecureBaseMediaUrl() { return 'https://example.com/media/'; }
    public function getCurrentUrl($fromStore = true) { return 'https://example.com/'; }
    public function getCurrentUrlNoScript($fromStore = true) { return 'https://example.com/'; }
    public function getBaseCurrency() { return null; }
    public function getDefaultCurrency() { return null; }
    public function getDefaultCurrencyCode() { return 'USD'; }
    public function getStoreName() { return 'Test Store ' . $this->storeId; }
    public function getStoreToBaseRate() { return 1.0; }
    public function getStoreToQuoteRate() { return 1.0; }
    public function getBaseToGlobalRate() { return 1.0; }
    public function getBaseToQuoteRate() { return 1.0; }
    public function getExtensionAttributes() { return null; }
    public function setExtensionAttributes($extensionAttributes) { return $this; }
    public function setId($id) { $this->storeId = $id; return $this; }
    public function setStoreId($storeId) { $this->storeId = $storeId; return $this; }
    public function setCode($code) { return $this; }
    public function setWebsiteId($websiteId) { return $this; }
    public function setGroupId($groupId) { return $this; }
    public function setStoreGroupId($storeGroupId) { return $this; }
    public function setName($name) { return $this; }
    public function setSortOrder($sortOrder) { return $this; }
    public function setIsActive($isActive) { return $this; }
    public function setLocaleCode($localeCode) { return $this; }
    public function setBaseCurrencyCode($baseCurrencyCode) { return $this; }
    public function setDefaultDisplayCurrencyCode($defaultDisplayCurrencyCode) { return $this; }
    public function setTimezone($timezone) { return $this; }
    public function setWeightUnit($weightUnit) { return $this; }
    public function setBaseUrl($baseUrl, $type = null) { return $this; }
    public function setBaseLinkUrl($baseLinkUrl) { return $this; }
    public function setBaseStaticUrl($baseStaticUrl) { return $this; }
    public function setBaseMediaUrl($baseMediaUrl) { return $this; }
    public function setSecureBaseUrl($secureBaseUrl, $type = null) { return $this; }
    public function setSecureBaseLinkUrl($secureBaseLinkUrl) { return $this; }
    public function setSecureBaseStaticUrl($secureBaseStaticUrl) { return $this; }
    public function setSecureBaseMediaUrl($secureBaseMediaUrl) { return $this; }
    public function setCurrentUrl($currentUrl) { return $this; }
    public function setBaseCurrency($baseCurrency) { return $this; }
    public function setDefaultCurrency($defaultCurrency) { return $this; }
    public function setDefaultCurrencyCode($defaultCurrencyCode) { return $this; }
    public function setStoreName($storeName) { return $this; }
    public function setStoreToBaseRate($storeToBaseRate) { return $this; }
    public function setStoreToQuoteRate($storeToQuoteRate) { return $this; }
    public function setBaseToGlobalRate($baseToGlobalRate) { return $this; }
    public function setBaseToQuoteRate($baseToQuoteRate) { return $this; }
}
