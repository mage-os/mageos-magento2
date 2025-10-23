<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogUrlRewrite\Test\Unit\Helper;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Api\Data\GroupInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\Data\WebsiteInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Mock class for StoreManagerInterface with all required methods
 */
class StoreManagerInterfaceTestHelper implements StoreManagerInterface
{
    /**
     * Mock method for setIsSingleStoreModeAllowed
     *
     * @param bool $value
     * @return void
     */
    public function setIsSingleStoreModeAllowed($value): void
    {
        // Mock implementation
    }

    /**
     * Mock method for hasSingleStore
     *
     * @return bool
     */
    public function hasSingleStore(): bool
    {
        return false;
    }

    /**
     * Mock method for isSingleStoreMode
     *
     * @return bool
     */
    public function isSingleStoreMode(): bool
    {
        return false;
    }

    /**
     * Mock method for getStore
     *
     * @param null|string|bool|int|StoreInterface $storeId
     * @return StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore($storeId = null): StoreInterface
    {
        // Return a mock store - this would need to be properly mocked in tests
        throw new NoSuchEntityException(__('Store not found'));
    }

    /**
     * Mock method for getStores
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return StoreInterface[]
     */
    public function getStores($withDefault = false, $codeKey = false): array
    {
        return [];
    }

    /**
     * Mock method for getWebsite
     *
     * @param null|bool|int|string|WebsiteInterface $websiteId
     * @return WebsiteInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWebsite($websiteId = null): WebsiteInterface
    {
        // Return a mock website - this would need to be properly mocked in tests
        throw new \Magento\Framework\Exception\LocalizedException(__('Website not found'));
    }

    /**
     * Mock method for getWebsites
     *
     * @param bool $withDefault
     * @param bool $codeKey
     * @return WebsiteInterface[]
     */
    public function getWebsites($withDefault = false, $codeKey = false): array
    {
        return [];
    }

    /**
     * Mock method for reinitStores
     *
     * @return void
     */
    public function reinitStores(): void
    {
        // Mock implementation
    }

    /**
     * Mock method for getDefaultStoreView
     *
     * @return StoreInterface|null
     */
    public function getDefaultStoreView(): ?StoreInterface
    {
        return null;
    }

    /**
     * Mock method for getGroup
     *
     * @param null|GroupInterface|string $groupId
     * @return GroupInterface
     */
    public function getGroup($groupId = null): GroupInterface
    {
        // Return a mock group - this would need to be properly mocked in tests
        throw new \Magento\Framework\Exception\LocalizedException(__('Group not found'));
    }

    /**
     * Mock method for getGroups
     *
     * @param bool $withDefault
     * @return GroupInterface[]
     */
    public function getGroups($withDefault = false): array
    {
        return [];
    }

    /**
     * Mock method for setCurrentStore
     *
     * @param string|int|StoreInterface $store
     * @return void
     */
    public function setCurrentStore($store): void
    {
        // Mock implementation
    }
}
