<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Model\Sample;

/**
 * Test helper for Downloadable Sample Model
 */
class SampleTestHelper extends Sample
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

    /**
     * Get base path
     *
     * @return string|null
     */
    public function getBasePath()
    {
        return $this->data['base_path'] ?? null;
    }

    /**
     * Set base path
     *
     * @param string $basePath
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->data['base_path'] = $basePath;
        return $this;
    }

    /**
     * Get base sample path
     *
     * @return string|null
     */
    public function getBaseSamplePath()
    {
        return $this->data['base_sample_path'] ?? null;
    }

    /**
     * Set base sample path
     *
     * @param string $baseSamplePath
     * @return $this
     */
    public function setBaseSamplePath($baseSamplePath)
    {
        $this->data['base_sample_path'] = $baseSamplePath;
        return $this;
    }
}