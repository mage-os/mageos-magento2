<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Link;

/**
 * Test helper for Downloadable Link
 *
 * Extends Link class and uses internal $data array for storage since parent
 * constructor is skipped. Parent has real methods (getTitle, setPrice, etc.)
 * which call getData/setData, so we override those to use our $data array.
 *
 * This allows parent methods to work: parent's getTitle() calls $this->getData('title')
 * which now reads from our $data array.
 *
 * Additionally provides custom methods that don't exist in parent (getStoreTitle,
 * setProductId, etc.) following the pattern: $this->data['key'] ?? null.
 *
 * Usage Example:
 * ```php
 * // Direct usage with data
 * $link = new LinkTestHelper(['id' => 1, 'title' => 'Link Title']);
 * $link->getTitle(); // Works! Calls parent's getTitle() → getData('title')
 *
 * // Or with mocking
 * $link = $this->createPartialMock(LinkTestHelper::class, ['getTitle']);
 * $link->method('getTitle')->willReturn('Mocked Title');
 * ```
 */
class LinkTestHelper extends Link
{
    /**
     * @var array Internal data storage
     */
    private $data = [];

    /**
     * Skip parent constructor to avoid dependencies
     *
     * Parent constructor requires Context, Registry, ExtensionFactory,
     * AttributeValueFactory, Resource, and Collection dependencies.
     *
     * @param array $data Optional initial data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Override setData to use internal array (parent's $_data not initialized)
     *
     * @param string|array $key
     * @param mixed $value
     * @return self
     */
    public function setData($key, $value = null): self
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Override getData to use internal array (parent's $_data not initialized)
     *
     * @param string $key
     * @param mixed $index
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = '', $index = null)
    {
        if ($key === '') {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Get ID - override to use our $data array
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Get product ID
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->data['product_id'] ?? null;
    }

    /**
     * Get store title
     *
     * @return string|null
     */
    public function getStoreTitle()
    {
        return $this->data['store_title'] ?? null;
    }

    /**
     * Get website price
     *
     * @return float|null
     */
    public function getWebsitePrice()
    {
        return $this->data['website_price'] ?? null;
    }

    /**
     * Check if has sample type
     *
     * @return bool
     */
    public function hasSampleType()
    {
        return (bool)($this->data['sample_type'] ?? false);
    }

    /**
     * Check if shareable
     *
     * @return bool
     */
    public function isShareable()
    {
        return (bool)($this->data['is_shareable'] ?? false);
    }

    /**
     * Get is unlimited
     *
     * @return bool|null
     */
    public function getIsUnlimited()
    {
        return $this->data['is_unlimited'] ?? null;
    }

    /**
     * Get product
     *
     * @return mixed
     */
    public function getProduct()
    {
        return $this->data['product'] ?? null;
    }

    /**
     * Set product ID
     *
     * @param int $value Product ID
     * @return self
     */
    public function setProductId($value): self
    {
        $this->data['product_id'] = $value;
        return $this;
    }

    /**
     * Set store ID
     *
     * @param int $storeId Store ID
     * @return self
     */
    public function setStoreId($storeId): self
    {
        $this->data['store_id'] = $storeId;
        return $this;
    }

    /**
     * Set website ID
     *
     * @param int $websiteId Website ID
     * @return self
     */
    public function setWebsiteId($websiteId): self
    {
        $this->data['website_id'] = $websiteId;
        return $this;
    }

    /**
     * Set product website IDs
     *
     * @param array $websiteIds Website IDs
     * @return self
     */
    public function setProductWebsiteIds($websiteIds): self
    {
        $this->data['product_website_ids'] = $websiteIds;
        return $this;
    }
}
