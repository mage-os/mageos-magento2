<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Link;

/**
 * Test helper class for Downloadable Link with custom methods
 *
 * This helper extends Link and adds custom methods that can be mocked
 * using PHPUnit's createPartialMock() for behavior verification.
 */
class LinkTestHelper extends Link
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Override load method
     *
     * @param mixed $id
     * @param string|null $field
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($id, $field = null): self
    {
        return $this;
    }

    /**
     * Override getId method
     *
     * @return mixed
     */
    public function getId()
    {
        return 1;
    }

    /**
     * Override getLinkType method
     *
     * @return string
     */
    public function getLinkType(): string
    {
        return 'url';
    }

    /**
     * Override getLinkUrl method
     *
     * @return string
     */
    public function getLinkUrl(): string
    {
        return 'http://example.com/link';
    }

    /**
     * Override getSampleUrl method
     *
     * @return string|null
     */
    public function getSampleUrl()
    {
        return null;
    }

    /**
     * Override getSampleType method
     *
     * @return string
     */
    public function getSampleType(): string
    {
        return 'url';
    }

    /**
     * Override getBasePath method
     *
     * @return string
     */
    public function getBasePath(): string
    {
        return '/base/path';
    }

    /**
     * Override getBaseSamplePath method
     *
     * @return string
     */
    public function getBaseSamplePath(): string
    {
        return '/base/sample/path';
    }

    /**
     * Override getLinkFile method
     *
     * @return string|null
     */
    public function getLinkFile()
    {
        return null;
    }

    /**
     * Override getSampleFile method
     *
     * @return string
     */
    public function getSampleFile(): string
    {
        return 'sample.pdf';
    }

    /**
     * Custom getProduct method for testing
     *
     * @return mixed
     */
    public function getProduct()
    {
        return null;
    }

    /**
     * Override getPrice method
     *
     * @return float|null
     */
    public function getPrice()
    {
        return null;
    }

    /**
     * Override __wakeup method
     *
     * @return void
     */
    public function __wakeup(): void
    {
        // Do nothing
    }

    /**
     * Custom getProductId method for testing
     *
     * @return int|null
     */
    public function getProductId()
    {
        return null;
    }

    /**
     * Custom getStoreTitle method for testing
     *
     * @return string|null
     */
    public function getStoreTitle()
    {
        return null;
    }

    /**
     * Override getData method
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        return null;
    }

    /**
     * Override save method
     *
     * @return self
     */
    public function save(): self
    {
        return $this;
    }

    /**
     * Custom setProductId method for testing
     *
     * @param int $productId
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setProductId($productId): self
    {
        return $this;
    }

    /**
     * Custom setStoreId method for testing
     *
     * @param int $storeId
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setStoreId($storeId): self
    {
        return $this;
    }

    /**
     * Custom setWebsiteId method for testing
     *
     * @param int $websiteId
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setWebsiteId($websiteId): self
    {
        return $this;
    }

    /**
     * Custom setProductWebsiteIds method for testing
     *
     * @param array $websiteIds
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setProductWebsiteIds($websiteIds): self
    {
        return $this;
    }

    /**
     * Custom getIsUnlimited method for testing
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getIsUnlimited()
    {
        return false;
    }

    /**
     * Override setData method
     *
     * @param mixed $key
     * @param mixed $value
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setData($key, $value = null): self
    {
        return $this;
    }
}
