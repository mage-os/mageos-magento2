<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\ProductVideo\Test\Unit\Block\Product\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\ProductVideo\Block\Product\View\Gallery;
use Magento\ProductVideo\Helper\Media;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GalleryTest extends TestCase
{
    /**
     * @var Context|MockObject
     */
    protected $contextMock;

    /**
     * @var ArrayUtils|MockObject
     */
    protected $arrayUtilsMock;

    /**
     * @var Media|MockObject
     */
    protected $mediaHelperMock;

    /**
     * @var EncoderInterface|MockObject
     */
    protected $jsonEncoderMock;

    /**
     * @var Registry|MockObject
     */
    protected $coreRegistry;

    /**
     * @var Gallery|MockObject
     */
    protected $gallery;

    /**
     * @var Product|MockObject
     */
    protected $productModelMock;

    /**
     * Set up
     */
    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->arrayUtilsMock = $this->createMock(ArrayUtils::class);
        $this->mediaHelperMock = $this->createMock(Media::class);
        $this->jsonEncoderMock = $this->createMock(EncoderInterface::class);
        $this->coreRegistry = $this->createMock(Registry::class);
        $this->contextMock->method('getRegistry')->willReturn($this->coreRegistry);

        $this->productModelMock = $this->createMock(Product::class);

        $this->gallery = $this->getMockBuilder(Gallery::class)
            ->onlyMethods(['getMediaGalleryDataJson', 'getVideoSettingsJson'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test getMediaGalleryDataJson()
     */
    public function testGetMediaGalleryDataJson()
    {
        $expectedJson = '[{"media_type":"external-video","video_url":"http://example.com/video.mp4"}]';
        $this->gallery->method('getMediaGalleryDataJson')->willReturn($expectedJson);
        $result = $this->gallery->getMediaGalleryDataJson();
        $this->assertEquals($expectedJson, $result);
    }

    /**
     * Test getMediaEmptyGalleryDataJson()
     */
    public function testGetMediaEmptyGalleryDataJson()
    {
        $expectedJson = '[]';
        $this->gallery->method('getMediaGalleryDataJson')->willReturn($expectedJson);
        $result = $this->gallery->getMediaGalleryDataJson();
        $this->assertEquals($expectedJson, $result);
    }

    /**
     * Test getVideoSettingsJson
     */
    public function testGetVideoSettingsJson()
    {
        $expectedJson = '{"playIfBase":1,"showRelated":0,"videoAutoRestart":0}';
        $this->gallery->method('getVideoSettingsJson')->willReturn($expectedJson);
        $result = $this->gallery->getVideoSettingsJson();
        $this->assertEquals($expectedJson, $result);
    }
}
