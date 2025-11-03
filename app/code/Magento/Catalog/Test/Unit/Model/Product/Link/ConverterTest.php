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
use Magento\Catalog\Test\Unit\Helper\ProductLinkInterfaceTestHelper;
use Magento\Framework\Api\ExtensionAttributesInterface;
use Magento\Framework\Api\Test\Unit\Helper\ExtensionAttributesTestHelper;
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
        $linkMock = new ProductLinkInterfaceTestHelper();
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
        $attributeMock = new ExtensionAttributesTestHelper();
        $linkMock->setExtensionAttributes($attributeMock);
        $attributeMock->setArrayData(['qty' => 33]);

        $this->assertEquals($linksAsArray, $this->converter->convertLinksToGroupedArray($productMock));
    }
}
