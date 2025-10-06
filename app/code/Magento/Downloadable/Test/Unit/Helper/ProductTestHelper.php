<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Catalog\Model\Product;

/**
 * Test helper class for Product with custom methods
 *
 * This helper extends Product and adds custom methods that can be mocked
 * using PHPUnit's createPartialMock() for behavior verification.
 */
class ProductTestHelper extends Product
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Custom setDownloadableData method for testing
     *
     * @param mixed $data
     * @return self
     */
    public function setDownloadableData($data): self
    {
        return $this;
    }

    /**
     * Override getExtensionAttributes method
     *
     * @return mixed
     */
    public function getExtensionAttributes()
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
     * Override getTypeInstance method
     *
     * @return mixed
     */
    public function getTypeInstance()
    {
        return null;
    }

    /**
     * Override getLinksTitle method
     *
     * @return string|null
     */
    public function getLinksTitle()
    {
        return null;
    }

    /**
     * Override getStoreId method
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return null;
    }

    /**
     * Override getSamplesTitle method
     *
     * @return string|null
     */
    public function getSamplesTitle()
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
     * Override getId method
     *
     * @return int|null
     */
    public function getId()
    {
        return null;
    }

    /**
     * Override getStore method
     *
     * @return mixed
     */
    public function getStore()
    {
        return null;
    }

    /**
     * Override getWebsiteIds method
     *
     * @return array
     */
    public function getWebsiteIds()
    {
        return [];
    }

    /**
     * Override getData method
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     */
    public function getData($key = '', $index = null)
    {
        return null;
    }

    /**
     * Override getSku method
     *
     * @return string|null
     */
    public function getSku()
    {
        return null;
    }

    /**
     * Custom getLinksPurchasedSeparately method for testing
     *
     * @return bool
     */
    public function getLinksPurchasedSeparately()
    {
        return false;
    }

    /**
     * Custom setIsCustomOptionChanged method for testing
     *
     * @param bool $changed
     * @return self
     */
    public function setIsCustomOptionChanged($changed = true): self
    {
        return $this;
    }

    /**
     * Custom setTypeHasRequiredOptions method for testing
     *
     * @param bool $hasRequired
     * @return self
     */
    public function setTypeHasRequiredOptions($hasRequired): self
    {
        return $this;
    }

    /**
     * Custom setRequiredOptions method for testing
     *
     * @param bool $required
     * @return self
     */
    public function setRequiredOptions($required): self
    {
        return $this;
    }

    /**
     * Custom getDownloadableData method for testing
     *
     * @return array|null
     */
    public function getDownloadableData()
    {
        return null;
    }

    /**
     * Custom setTypeHasOptions method for testing
     *
     * @param bool $hasOptions
     * @return self
     */
    public function setTypeHasOptions($hasOptions): self
    {
        return $this;
    }

    /**
     * Custom setLinksExist method for testing
     *
     * @param bool $exist
     * @return self
     */
    public function setLinksExist($exist): self
    {
        return $this;
    }

    /**
     * Custom getDownloadableLinks method for testing
     *
     * @return array|null
     */
    public function getDownloadableLinks()
    {
        return null;
    }

    /**
     * Override getResource method
     *
     * @return mixed
     */
    public function getResource()
    {
        return null;
    }

    /**
     * Override canAffectOptions method
     *
     * @param mixed $value
     * @return bool
     */
    public function canAffectOptions($value = null)
    {
        return false;
    }

    /**
     * Override getCustomOption method
     *
     * @param string $code
     * @return mixed
     */
    public function getCustomOption($code)
    {
        return null;
    }

    /**
     * Override addCustomOption method
     *
     * @param string $code
     * @param mixed $value
     * @param mixed $product
     * @return self
     */
    public function addCustomOption($code, $value, $product = null): self
    {
        return $this;
    }

    /**
     * Override getEntityId method
     *
     * @return int|null
     */
    public function getEntityId()
    {
        return null;
    }
}
