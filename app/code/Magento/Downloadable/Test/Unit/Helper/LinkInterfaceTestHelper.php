<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Downloadable\Test\Unit\Helper;

use Magento\Downloadable\Api\Data\LinkInterface;

/**
 * Test helper class for LinkInterface with custom methods
 */
class LinkInterfaceTestHelper implements LinkInterface
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return 'Test Title';
    }

    /**
     * @inheritdoc
     */
    public function setTitle($title)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSortOrder()
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsShareable()
    {
        return 1;
    }

    /**
     * @inheritdoc
     */
    public function setIsShareable($isShareable)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPrice()
    {
        return 0.0;
    }

    /**
     * @inheritdoc
     */
    public function setPrice($price)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getNumberOfDownloads()
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function setNumberOfDownloads($numberOfDownloads)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLinkType()
    {
        return 'url';
    }

    /**
     * @inheritdoc
     */
    public function setLinkType($linkType)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLinkFile()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setLinkFile($linkFile)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLinkFileContent()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setLinkFileContent(?\Magento\Downloadable\Api\Data\File\ContentInterface $linkFileContent = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getLinkUrl()
    {
        return 'http://example.com';
    }

    /**
     * @inheritdoc
     */
    public function setLinkUrl($linkUrl)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSampleType()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setSampleType($sampleType)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSampleFile()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setSampleFile($sampleFile)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSampleFileContent()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setSampleFileContent(
        ?\Magento\Downloadable\Api\Data\File\ContentInterface $sampleFileContent = null
    ) {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSampleUrl()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setSampleUrl($sampleUrl)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes($extensionAttributes)
    {
        return $this;
    }

    /**
     * Custom getWebsitePrice method for testing
     *
     * @return float|null
     */
    public function getWebsitePrice()
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
     * Custom hasSampleType method for testing
     *
     * @return bool
     */
    public function hasSampleType()
    {
        return false;
    }

    /**
     * Custom isShareable method for testing
     *
     * @return bool
     */
    public function isShareable()
    {
        return false;
    }
}
