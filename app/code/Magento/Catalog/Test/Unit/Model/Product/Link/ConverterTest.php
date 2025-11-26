<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Link;

use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\Data\ProductLinkExtensionInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Link\Converter;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    use MockCreationTrait;
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
        $info = [100, 300, 500];
        $infoFinal = [100, 300, 500, 'id' => $linkedProductId, 'qty' => 33];
        $linksAsArray = [$linkType => [$infoFinal]];

        $linkData = [];
        $linkMock = $this->createPartialMockWithReflection(
            ProductLinkInterface::class,
            ['setData', 'getData', 'setLinkType', 'getLinkType', 'setLinkedProductSku',
             'getLinkedProductSku', 'setExtensionAttributes', 'getExtensionAttributes',
             'getSku', 'setSku', 'getPosition', 'setPosition', 'getLinkedProductType',
             'setLinkedProductType']
        );
        $linkMock->method('setData')->willReturnCallback(function ($data) use (&$linkData, $linkMock) {
            $linkData['data'] = $data;
            return $linkMock;
        });
        $linkMock->method('getData')->willReturnCallback(function () use (&$linkData) {
            return $linkData['data'] ?? [];
        });
        $linkMock->method('setLinkType')->willReturnCallback(function ($type) use (&$linkData, $linkMock) {
            $linkData['link_type'] = $type;
            return $linkMock;
        });
        $linkMock->method('getLinkType')->willReturnCallback(function () use (&$linkData) {
            return $linkData['link_type'] ?? null;
        });
        $linkMock->method('setLinkedProductSku')->willReturnCallback(function ($sku) use (&$linkData, $linkMock) {
            $linkData['linked_product_sku'] = $sku;
            return $linkMock;
        });
        $linkMock->method('getLinkedProductSku')->willReturnCallback(function () use (&$linkData) {
            return $linkData['linked_product_sku'] ?? null;
        });
        $linkMock->method('setExtensionAttributes')->willReturnCallback(function ($attr) use (&$linkData, $linkMock) {
            $linkData['extension_attributes'] = $attr;
            return $linkMock;
        });
        $linkMock->method('getExtensionAttributes')->willReturnCallback(function () use (&$linkData) {
            return $linkData['extension_attributes'] ?? null;
        });
        $linkMock->method('getSku')->willReturn(null);
        $linkMock->method('setSku')->willReturnSelf();
        $linkMock->method('getPosition')->willReturn(0);
        $linkMock->method('setPosition')->willReturnSelf();
        $linkMock->method('getLinkedProductType')->willReturn(null);
        $linkMock->method('setLinkedProductType')->willReturnSelf();

        $attrData = [];
        $attributeMock = $this->createPartialMockWithReflection(
            ProductLinkExtensionInterface::class,
            ['setArrayData', 'getArrayData', '__toArray', 'getQty', 'setQty']
        );
        $attributeMock->method('setArrayData')->willReturnCallback(function ($data) use (&$attrData, $attributeMock) {
            $attrData = $data;
            return $attributeMock;
        });
        $attributeMock->method('getArrayData')->willReturnCallback(function () use (&$attrData) {
            return $attrData;
        });
        $attributeMock->method('__toArray')->willReturnCallback(function () use (&$attrData) {
            return $attrData;
        });
        $attributeMock->method('getQty')->willReturnCallback(function () use (&$attrData) {
            return $attrData['qty'] ?? null;
        });
        $attributeMock->method('setQty')->willReturnCallback(function ($qty) use (&$attrData, $attributeMock) {
            $attrData['qty'] = $qty;
            return $attributeMock;
        });

        $basicData = [$linkMock];
        $linkedProductMock = $this->createMock(Product::class);
        $associatedProducts = [$linkedProductSku => $linkedProductMock];

        $typeMock = $this->createMock(AbstractType::class);

        $productMock = $this->createMock(Product::class);
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
        $linkMock->expects($this->once())
            ->method('getData')
            ->willReturn($info);
        $linkMock->setLinkType($linkType);
        $linkMock->expects($this->exactly(2))
            ->method('getLinkType')
            ->willReturn($linkType);
        $linkMock->setLinkedProductSku($linkedProductSku);
        $linkMock->expects($this->once())
            ->method('getLinkedProductSku')
            ->willReturn($linkedProductSku);
        $linkedProductMock->expects($this->once())
            ->method('getId')
            ->willReturn($linkedProductId);
        $linkMock->setExtensionAttributes($attributeMock);
        $attributeMock->setArrayData(['qty' => 33]);
        $linkMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($attributeMock);
        $attributeMock->expects($this->once())
            ->method('__toArray')
            ->willReturn(['qty' => 33]);

        $this->assertEquals($linksAsArray, $this->converter->convertLinksToGroupedArray($productMock));
    }
}
