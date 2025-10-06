<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Link\Purchased\Item;

/**
 * Test helper class for Downloadable Link Purchased Item with custom methods
 *
 * This helper extends Item and adds custom methods that can be mocked
 * using PHPUnit's createPartialMock() for behavior verification.
 */
class ItemTestHelper extends Item
{
    /**
     * Skip parent constructor to avoid dependencies
     */
    public function __construct()
    {
        // Skip parent constructor
    }

    /**
     * Override getProductId method
     *
     * @return mixed
     */
    public function getProductId()
    {
        return 1;
    }

    /**
     * Override getPurchasedId method
     *
     * @return mixed
     */
    public function getPurchasedId()
    {
        return 1;
    }

    /**
     * Override getNumberOfDownloadsBought method
     *
     * @return int
     */
    public function getNumberOfDownloadsBought(): int
    {
        return 5;
    }

    /**
     * Override getNumberOfDownloadsUsed method
     *
     * @return int
     */
    public function getNumberOfDownloadsUsed(): int
    {
        return 2;
    }

    /**
     * Override getStatus method
     *
     * @return mixed
     */
    public function getStatus()
    {
        return 'available';
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
        return 'http://example.com/download';
    }

    /**
     * Override getLinkFile method
     *
     * @return string
     */
    public function getLinkFile(): string
    {
        return 'file.pdf';
    }

    /**
     * Override setNumberOfDownloadsUsed method
     *
     * @param int $count
     * @return self
     */
    public function setNumberOfDownloadsUsed($count): self
    {
        return $this;
    }

    /**
     * Override setStatus method
     *
     * @param string $status
     * @return self
     */
    public function setStatus($status): self
    {
        return $this;
    }

    /**
     * Override load method
     *
     * @param mixed $id
     * @param string|null $field
     * @return self
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
     * Override save method
     *
     * @return self
     */
    public function save(): self
    {
        return $this;
    }

    /**
     * Custom getOrderItemId method for testing
     *
     * @return int|null
     */
    public function getOrderItemId()
    {
        return null;
    }

    /**
     * Custom setNumberOfDownloadsBought method for testing
     *
     * @param int $number
     * @return self
     */
    public function setNumberOfDownloadsBought($number): self
    {
        return $this;
    }
}
