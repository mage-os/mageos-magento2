<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ProductVideo\Test\Unit\Helper;

use Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryExtensionInterface;
use Magento\Framework\Api\Data\VideoContentInterface;

/**
 * Mock class for ProductAttributeMediaGalleryEntryExtension with video methods
 */
class ProductAttributeMediaGalleryEntryExtensionMock implements ProductAttributeMediaGalleryEntryExtensionInterface
{
    /**
     * @var array
     */
    private array $data = [];

    /**
     * Mock method for setVideoContent
     *
     * @param VideoContentInterface $videoContent
     * @return $this
     */
    public function setVideoContent(VideoContentInterface $videoContent)
    {
        $this->data['video_content'] = $videoContent;
        return $this;
    }

    /**
     * Mock method for getVideoContent
     *
     * @return VideoContentInterface|null
     */
    public function getVideoContent(): ?VideoContentInterface
    {
        return $this->data['video_content'] ?? null;
    }

    /**
     * Mock method for getVideoProvider
     *
     * @return string|null
     */
    public function getVideoProvider()
    {
        return $this->data['video_provider'] ?? null;
    }

    /**
     * Mock method for setVideoProvider
     *
     * @param string $videoProvider
     * @return $this
     */
    public function setVideoProvider($videoProvider)
    {
        $this->data['video_provider'] = $videoProvider;
        return $this;
    }

    /**
     * Get data
     *
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        if ($key === null) {
            return $this->data;
        }
        return $this->data[$key] ?? null;
    }

    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    public function setData($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }
        return $this;
    }
}
