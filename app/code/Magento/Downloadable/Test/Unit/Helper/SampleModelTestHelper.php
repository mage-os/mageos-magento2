<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Sample;

/**
 * Test helper class for Sample model with custom methods
 */
class SampleModelTestHelper extends Sample
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
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
     * Override __wakeup method
     *
     * @return void
     */
    public function __wakeup(): void
    {
        // Do nothing
    }

    /**
     * Override getId method
     *
     * @return int|null
     */
    public function getId()
    {
        return null;
    }

    /**
     * Override load method
     *
     * @param mixed $id
     * @param mixed $field
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function load($id, $field = null): self
    {
        return $this;
    }

    /**
     * Override save method
     *
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function save(): self
    {
        return $this;
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
     * Override getTitle method
     *
     * @return string|null
     */
    public function getTitle()
    {
        return null;
    }

    /**
     * Override getSampleType method
     *
     * @return string|null
     */
    public function getSampleType()
    {
        return null;
    }

    /**
     * Override getSampleFile method
     *
     * @return string|null
     */
    public function getSampleFile()
    {
        return null;
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
     * Override getSortOrder method
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return null;
    }

    /**
     * Custom setProductId method for testing
     *
     * @param int $productId
     * @return self
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
     */
    public function setStoreId($storeId): self
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
     * Custom setNumberOfDownloads method for testing
     *
     * @param int $downloads
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setNumberOfDownloads($downloads): self
    {
        return $this;
    }

    /**
     * Custom setLinkFile method for testing
     *
     * @param string $file
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setLinkFile($file): self
    {
        return $this;
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

    /**
     * Override setSampleType method
     *
     * @param string $type
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSampleType($type): self
    {
        return $this;
    }

    /**
     * Override setSampleUrl method
     *
     * @param string $url
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSampleUrl($url): self
    {
        return $this;
    }

    /**
     * Override setSampleFile method
     *
     * @param string $file
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSampleFile($file): self
    {
        return $this;
    }

    /**
     * Override setTitle method
     *
     * @param string $title
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTitle($title): self
    {
        return $this;
    }

    /**
     * Override setSortOrder method
     *
     * @param int $sortOrder
     * @return self
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSortOrder($sortOrder): self
    {
        return $this;
    }
}
