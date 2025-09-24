<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Link;

use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Link\Converter;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Api\ExtensionAttributesInterface;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    protected $converter;

    protected function setUp(): void
    {
        $this->converter = new Converter();
    }

    public function testConvertLinksToGroupedArray()
    {
        $linkedProductSku = 'linkedProductSample';
        $linkedProductId = '2016';
        $linkType = 'associated';
        /** @var ProductLinkInterface $linkMock */
        $linkMock = new class {
            private $data = null;
            private $linkType = null;
            private $linkedProductSku = null;
            private $extensionAttributes = null;
            
            public function getData()
            {
                return $this->data;
            }
            
            public function setData($data)
            {
                $this->data = $data;
                return $this;
            }
            
            public function getLinkType()
            {
                return $this->linkType;
            }
            
            public function setLinkType($linkType)
            {
                $this->linkType = $linkType;
                return $this;
            }
            
            public function getLinkedProductSku()
            {
                return $this->linkedProductSku;
            }
            
            public function setLinkedProductSku($sku)
            {
                $this->linkedProductSku = $sku;
                return $this;
            }
            
            public function getExtensionAttributes()
            {
                return $this->extensionAttributes;
            }
            
            public function setExtensionAttributes($attributes)
            {
                $this->extensionAttributes = $attributes;
                return $this;
            }
        };
        $basicData = [$linkMock];
        $linkedProductMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $associatedProducts = [$linkedProductSku => $linkedProductMock];
        $info = [100, 300, 500];
        $infoFinal = [100, 300, 500, 'id' => $linkedProductId, 'qty' => 33];
        $linksAsArray = [$linkType => [$infoFinal]];

        $typeMock = $this->createMock(AbstractType::class);

        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productMock->expects($this->once())
            ->method('getProductLinks')
            ->willReturn($basicData);
        $productMock->expects($this->once())
            ->method('getTypeInstance')
            ->willReturn($typeMock);
        $typeMock->expects($this->once())
            ->method('getAssociatedProducts')
            ->with($productMock)
            ->willReturn($associatedProducts);
        $linkedProductMock->expects($this->once())
            ->method('getSku')
            ->willReturn($linkedProductSku);
        $linkMock->setData($info);
        $linkMock->setLinkType($linkType);
        $linkMock->setLinkedProductSku($linkedProductSku);
        $linkedProductMock->expects($this->once())
            ->method('getId')
            ->willReturn($linkedProductId);
        /** @var ExtensionAttributesInterface $attributeMock */
        $attributeMock = new class {
            private $arrayData = null;
            
            public function __toArray()
            {
                return $this->arrayData;
            }
            
            public function setArrayData($data)
            {
                $this->arrayData = $data;
                return $this;
            }
        };
        $linkMock->setExtensionAttributes($attributeMock);
        $attributeMock->setArrayData(['qty' => 33]);

        $this->assertEquals($linksAsArray, $this->converter->convertLinksToGroupedArray($productMock));
    }
}
