<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\ProductRepository;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\DeleteValidator;
use Magento\Catalog\Model\Product\Gallery\Processor;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Catalog\Model\ProductRepository\MediaGalleryProcessor;
use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\Api\ImageContent;
use Magento\Framework\Api\ImageProcessorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MediaGalleryProcessorTest extends TestCase
{
    /**
     * @var MediaGalleryProcessor
     */
    private $galleryProcessor;

    /**
     * @var Processor|MockObject
     */
    private $processorMock;

    /**
     * @var ImageContentInterfaceFactory|MockObject
     */
    private $contentFactoryMock;

    /**
     * @var ImageProcessorInterface|MockObject
     */
    private $imageProcessorMock;

    /**
     * @var DeleteValidator|MockObject
     */
    private $deleteValidatorMock;

    /**
     * @var Product|MockObject
     */
    private $productMock;

    protected function setUp(): void
    {
        $this->processorMock = $this->getMockBuilder(Processor::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->contentFactoryMock = $this->getMockBuilder(ImageContentInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->imageProcessorMock = $this->getMockBuilder(ImageProcessorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->deleteValidatorMock = $this->getMockBuilder(DeleteValidator::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->productMock = new class extends Product {
            private $mediaGallery = [];
            private $mediaAttributes = [];
            private $hasGalleryAttribute = true;
            private $mediaConfig = null;
            
            public function __construct()
            {
            }
            
            public function getMediaGallery()
            {
                return $this->mediaGallery;
            }
            
            public function setMediaGallery($mediaGallery)
            {
                $this->mediaGallery = $mediaGallery;
                return $this;
            }
            
            public function hasGalleryAttribute()
            {
                return $this->hasGalleryAttribute;
            }
            
            public function setHasGalleryAttribute($hasGalleryAttribute)
            {
                $this->hasGalleryAttribute = $hasGalleryAttribute;
                return $this;
            }
            
            public function getMediaConfig()
            {
                return $this->mediaConfig;
            }
            
            public function setMediaConfig($mediaConfig)
            {
                $this->mediaConfig = $mediaConfig;
                return $this;
            }
            
            public function getMediaAttributes()
            {
                return $this->mediaAttributes;
            }
            
            public function setMediaAttributes($mediaAttributes)
            {
                $this->mediaAttributes = $mediaAttributes;
                return $this;
            }
        };

        $this->galleryProcessor = new MediaGalleryProcessor(
            $this->processorMock,
            $this->contentFactoryMock,
            $this->imageProcessorMock,
            $this->deleteValidatorMock
        );
    }

    /**
     * The media gallery array should not have "removed" key while adding the new entry
     *
     * @return void
     */
    public function testProcessMediaGallery(): void
    {
        $initialExitingEntry = [
            'value_id' => 5,
            "label" => "new_label_text",
            'file' => 'filename1',
            'position' => 10,
            'disabled' => false,
            'types' => ['image', 'small_image']
        ];
        $newEntriesData = [
            'images' => [
                $initialExitingEntry,
                [
                    'value_id' => null,
                    'label' => "label_text",
                    'position' => 10,
                    'disabled' => false,
                    'types' => ['image', 'small_image'],
                    'content' => [
                        'data' => [
                            ImageContentInterface::NAME => 'filename',
                            ImageContentInterface::TYPE => 'image/jpeg',
                            ImageContentInterface::BASE64_ENCODED_DATA => 'encoded_content'
                        ]
                    ],
                    'media_type' => 'media_type'
                ]
            ]
        ];
        $newExitingEntriesData = [
            'images' => [
                $initialExitingEntry,
                [
                    'value_id' => 6,
                    "label" => "label_text2",
                    'file' => 'filename2',
                    'position' => 10,
                    'disabled' => false,
                    'types' => ['image', 'small_image']
                ]
            ]
        ];
        $this->productMock->setMediaGallery($newExitingEntriesData['images']);
        $this->productMock->setMediaAttributes(["image" => "imageAttribute", "small_image" => "small_image_attribute"]);
        $this->productMock->setHasGalleryAttribute(true);
        $mediaTmpPath = '/tmp';
        $absolutePath = '/a/b/filename.jpg';
        $this->processorMock->expects($this->once())->method('clearMediaAttribute')
            ->with($this->productMock, ['image', 'small_image']);
        $mediaConfigMock = $this->getMockBuilder(Config::class)->disableOriginalConstructor()->getMock();
        $mediaConfigMock->expects($this->once())->method('getTmpMediaShortUrl')->with($absolutePath)
            ->willReturn($mediaTmpPath . $absolutePath);
        $this->productMock->setMediaConfig($mediaConfigMock);
        //verify new entries
        $contentDataObject = $this->getMockBuilder(ImageContent::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();
        $this->contentFactoryMock->expects($this->once())->method('create')->willReturn($contentDataObject);
        $this->imageProcessorMock->expects($this->once())->method('processImageContent')->willReturn($absolutePath);
        $imageFileUri = $mediaTmpPath . $absolutePath;
        $this->processorMock->expects($this->once())->method('addImage')
            ->willReturnCallback(
                function ($product, $imageFileUri) use ($newEntriesData) {
                    foreach ($product['media_gallery']['images'] as $entry) {
                        if (isset($entry['value_id'])) {
                            $this->assertArrayNotHasKey('removed', $entry);
                        }
                    }
                    $this->productMock->setData('media_gallery', $newEntriesData);
                    return $imageFileUri;
                }
            );
        $this->processorMock->expects($this->once())->method('updateImage')
            ->with(
                $this->productMock,
                $imageFileUri,
                [
                    'label' => 'label_text',
                    'position' => 10,
                    'disabled' => false,
                    'media_type' => 'media_type',
                ]
            );
        $this->galleryProcessor->processMediaGallery($this->productMock, $newEntriesData['images']);
    }
}
