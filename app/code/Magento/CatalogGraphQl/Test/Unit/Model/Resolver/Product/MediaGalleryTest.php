<?php
/**
 * Copyright 2023 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\CatalogGraphQl\Test\Unit\Model\Resolver\Product;

use PHPUnit\Framework\Attributes\DataProvider;
use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Gallery\Entry;
use Magento\CatalogGraphQl\Model\Resolver\Product\MediaGallery;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\GraphQl\Config\Element\Field;

class MediaGalleryTest extends TestCase
{
    /**
     * @var Field|MockObject
     */
    private Field|MockObject $fieldMock;

    /**
     * @var ContextInterface|MockObject
     */
    private ContextInterface|MockObject $contextMock;

    /**
     * @var ResolveInfo|MockObject
     */
    private ResolveInfo|MockObject $infoMock;

    /**
     * @var Product|MockObject
     */
    private Product|MockObject $productMock;

    /**
     * @var MediaGallery
     */
    private MediaGallery $mediaGallery;

    protected function setUp(): void
    {
        $this->fieldMock = $this->createMock(Field::class);
        $this->contextMock = $this->createMock(ContextInterface::class);
        $this->infoMock = $this->createMock(ResolveInfo::class);
        $this->productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->mediaGallery = new MediaGallery();
    }

    /**
     * @param $expected
     * @param $productName
     * @return void
     * @throws Exception
     */
    #[DataProvider('dataProviderForResolve')]
    public function testResolve($expected, $productName): void
    {
        $existingEntryMock = new class implements \Magento\Catalog\Api\Data\ProductAttributeMediaGalleryEntryInterface {
            private $data = [];
            private $extensionAttributes = null;
            
            public function __construct() {}
            
            public function getData($key = '', $index = null) {
                if ($key === '') {
                    return $this->data;
                }
                return $this->data[$key] ?? null;
            }
            
            public function setData($key, $value = null) {
                if (is_array($key)) {
                    $this->data = array_merge($this->data, $key);
                } else {
                    $this->data[$key] = $value;
                }
                return $this;
            }
            
            public function getExtensionAttributes() {
                return $this->extensionAttributes;
            }
            
            public function setExtensionAttributes($extensionAttributes) {
                $this->extensionAttributes = $extensionAttributes;
                return $this;
            }
            
            public function getId() {
                return $this->getData('id');
            }
            
            public function setId($id) {
                return $this->setData('id', $id);
            }
            
            public function getMediaType() {
                return $this->getData('media_type');
            }
            
            public function setMediaType($mediaType) {
                return $this->setData('media_type', $mediaType);
            }
            
            public function getLabel() {
                return $this->getData('label');
            }
            
            public function setLabel($label) {
                return $this->setData('label', $label);
            }
            
            public function getPosition() {
                return $this->getData('position');
            }
            
            public function setPosition($position) {
                return $this->setData('position', $position);
            }
            
            public function isDisabled() {
                return $this->getData('disabled');
            }
            
            public function setDisabled($disabled) {
                return $this->setData('disabled', $disabled);
            }
            
            public function getTypes() {
                return $this->getData('types');
            }
            
            public function setTypes(?array $types = null) {
                return $this->setData('types', $types);
            }
            
            public function getFile() {
                return $this->getData('file');
            }
            
            public function setFile($file) {
                return $this->setData('file', $file);
            }
            
            public function getContent() {
                return $this->getData('content');
            }
            
            public function setContent($content) {
                return $this->setData('content', $content);
            }
        };
        $existingEntryMock->setData($expected);
        $existingEntryMock->setExtensionAttributes(false);
        $this->productMock->method('getName')->willReturn($productName);
        $this->productMock->method('getMediaGalleryEntries')->willReturn([$existingEntryMock]);
        $result = $this->mediaGallery->resolve(
            $this->fieldMock,
            $this->contextMock,
            $this->infoMock,
            [
                'model' => $this->productMock
            ],
            []
        );
        $this->assertNotEmpty($result);
        $this->assertEquals($productName, $result[0]['label']);
    }

    /**
     * @return array
     */
    public static function dataProviderForResolve(): array
    {
        return [
            [
                [
                    "file" => "/w/b/wb01-black-0.jpg",
                    "media_type" => "image",
                    "label" => null,
                    "position" => "1",
                    "disabled" => "0",
                    "types" => [
                        "image",
                        "small_image"
                    ],
                    "id" => "11"
                ],
                "TestImage"
            ],
            [
                [
                    "file" => "/w/b/wb01-black-0.jpg",
                    "media_type" => "image",
                    "label" => "HelloWorld",
                    "position" => "1",
                    "disabled" => "0",
                    "types" => [
                        "image",
                        "small_image"
                    ],
                    "id" => "11"
                ],
                "HelloWorld"
            ]
        ];
    }
}
