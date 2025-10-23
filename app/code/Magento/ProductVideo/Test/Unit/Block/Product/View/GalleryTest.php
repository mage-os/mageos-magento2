<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProductVideo\Test\Unit\Block\Product\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\ImagesConfigFactoryInterface;
use Magento\Catalog\Model\Product\Image\UrlBuilder;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\DataObject;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
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
     * |\Magento\ProductVideo\Block\Adminhtml\Product\Video\Gallery
     *
     * @var ObjectManager
     */
    protected $gallery;

    /**
     * @var Product|MockObject
     */
    protected $productModelMock;

    /**
     * @var MockObject|ImagesConfigFactoryInterface
     */
    protected $imagesConfigFactoryMock;

    /**
     * @var MockObject|UrlBuilder
     */
    protected $urlBuilderMock;

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
        $this->productModelMock = $this->createMock(Product::class);
        $this->imagesConfigFactoryMock = $this->createMock(ImagesConfigFactoryInterface::class);
        $this->urlBuilderMock = $this->createMock(UrlBuilder::class);

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
        $expectedJson = '{"test":"data"}';
        $this->gallery->expects($this->once())->method('getMediaGalleryDataJson')->willReturn($expectedJson);
        $result = $this->gallery->getMediaGalleryDataJson();
        $this->assertEquals($expectedJson, $result);
    }

    /**
     * Test getMediaEmptyGalleryDataJson()
     */
    public function testGetMediaEmptyGalleryDataJson()
    {
        $expectedJson = '[]';
        $this->gallery->expects($this->once())->method('getMediaGalleryDataJson')->willReturn($expectedJson);
        $result = $this->gallery->getMediaGalleryDataJson();
        $this->assertEquals($expectedJson, $result);
    }

    /**
     * Test getVideoSettingsJson
     */
    public function testGetVideoSettingsJson()
    {
        $expectedSettings = '{"playIfBase":1,"showRelated":0,"videoAutoRestart":0}';
        $this->gallery->expects($this->once())->method('getVideoSettingsJson')->willReturn($expectedSettings);
        $result = $this->gallery->getVideoSettingsJson();
        $this->assertEquals($expectedSettings, $result);
    }
}
