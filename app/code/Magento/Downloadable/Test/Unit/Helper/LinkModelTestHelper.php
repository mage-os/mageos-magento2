<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Link;

/**
 * Test helper for Downloadable Link Model
 */
class LinkModelTestHelper extends Link
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        // Skip parent constructor to avoid dependencies
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->data['id'] ?? null;
    }

    /**
     * Set ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->data['id'] = $id;
        return $this;
    }

    /**
     * Get title
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->data['title'] ?? null;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title)
    {
        $this->data['title'] = $title;
        return $this;
    }

    /**
     * Get price
     *
     * @return string|null
     */
    public function getPrice()
    {
        return $this->data['price'] ?? null;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return $this
     */
    public function setPrice($price)
    {
        $this->data['price'] = $price;
        return $this;
    }

    /**
     * Get number of downloads
     *
     * @return string|null
     */
    public function getNumberOfDownloads()
    {
        return $this->data['number_of_downloads'] ?? null;
    }

    /**
     * Set number of downloads
     *
     * @param string $numberOfDownloads
     * @return $this
     */
    public function setNumberOfDownloads($numberOfDownloads)
    {
        $this->data['number_of_downloads'] = $numberOfDownloads;
        return $this;
    }

    /**
     * Get link URL
     *
     * @return string|null
     */
    public function getLinkUrl()
    {
        return $this->data['link_url'] ?? null;
    }

    /**
     * Set link URL
     *
     * @param string $linkUrl
     * @return $this
     */
    public function setLinkUrl($linkUrl)
    {
        $this->data['link_url'] = $linkUrl;
        return $this;
    }

    /**
     * Get link type
     *
     * @return string|null
     */
    public function getLinkType()
    {
        return $this->data['link_type'] ?? null;
    }

    /**
     * Set link type
     *
     * @param string $linkType
     * @return $this
     */
    public function setLinkType($linkType)
    {
        $this->data['link_type'] = $linkType;
        return $this;
    }

    /**
     * Get sample URL
     *
     * @return string|null
     */
    public function getSampleUrl()
    {
        return $this->data['sample_url'] ?? null;
    }

    /**
     * Set sample URL
     *
     * @param string $sampleUrl
     * @return $this
     */
    public function setSampleUrl($sampleUrl)
    {
        $this->data['sample_url'] = $sampleUrl;
        return $this;
    }

    /**
     * Get sample type
     *
     * @return string|null
     */
    public function getSampleType()
    {
        return $this->data['sample_type'] ?? null;
    }

    /**
     * Set sample type
     *
     * @param string $sampleType
     * @return $this
     */
    public function setSampleType($sampleType)
    {
        $this->data['sample_type'] = $sampleType;
        return $this;
    }

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->data['sort_order'] ?? null;
    }

    /**
     * Set sort order
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->data['sort_order'] = $sortOrder;
        return $this;
    }

    /**
     * Get link file
     *
     * @return string|null
     */
    public function getLinkFile()
    {
        return $this->data['link_file'] ?? null;
    }

    /**
     * Set link file
     *
     * @param string $linkFile
     * @return $this
     */
    public function setLinkFile($linkFile)
    {
        $this->data['link_file'] = $linkFile;
        return $this;
    }

    /**
     * Get sample file
     *
     * @return string|null
     */
    public function getSampleFile()
    {
        return $this->data['sample_file'] ?? null;
    }

    /**
     * Set sample file
     *
     * @param string $sampleFile
     * @return $this
     */
    public function setSampleFile($sampleFile)
    {
        $this->data['sample_file'] = $sampleFile;
        return $this;
    }

    /**
     * Get store title
     *
     * @return string|null
     */
    public function getStoreTitle()
    {
        return $this->data['store_title'] ?? 'Test Store Title';
    }

    /**
     * Set store title
     *
     * @param string $storeTitle
     * @return $this
     */
    public function setStoreTitle($storeTitle)
    {
        $this->data['store_title'] = $storeTitle;
        return $this;
    }
}
