<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Model\Product\Website;

use Magento\Catalog\Api\Data\ProductExtensionInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Website\ReadHandler;
use Magento\Catalog\Model\ResourceModel\Product as ResourceModel;
use Magento\Catalog\Model\ResourceModel\Product\Website\Link;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ReadHandlerTest extends TestCase
{
    /** @var ResourceModel\Website\Link|MockObject */
    private $websiteLinkMock;

    /** @var MockObject  */
    private $extensionAttributesMock;

    /** @var  ReadHandler  */
    private $readHandler;

    protected function setUp(): void
    {
        $this->websiteLinkMock = $this->getMockBuilder(Link::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var ProductExtensionInterface $this->extensionAttributesMock */
        $this->extensionAttributesMock = $this->createStub(ProductExtensionInterface::class);
        $websiteIds = null;
        $this->extensionAttributesMock->method('setWebsiteIds')->willReturnCallback(
            function ($value) use (&$websiteIds) {
                $websiteIds = $value;
                return $this->extensionAttributesMock;
            }
        );
        $this->extensionAttributesMock->method('getWebsiteIds')->willReturnCallback(
            function () use (&$websiteIds) {
                return $websiteIds;
            }
        );
        $this->readHandler = new ReadHandler($this->websiteLinkMock);
    }

    public function testExecuteWithNonCachedExtensionAttributes()
    {
        $productId = 1;
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $product->expects($this->atLeastOnce())
            ->method('getId')
            ->willReturn($productId);
        $websiteIds = [1,2];
        $this->websiteLinkMock->expects($this->once())
            ->method("getWebsiteIdsByProductId")
            ->with($productId)
            ->willReturn($websiteIds);
        $product->expects($this->exactly(2))
            ->method('getExtensionAttributes')
            ->willReturn($this->extensionAttributesMock);
        $this->extensionAttributesMock->setWebsiteIds(null);

        $product->expects($this->once())
            ->method('setExtensionAttributes')
            ->with($this->extensionAttributesMock);

        $this->assertEquals($this->readHandler->execute($product, []), $product);
    }

    public function testExecuteWithCachedWebsiteIds()
    {
        $product = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $websiteIds = [1,2];
        $this->extensionAttributesMock->setWebsiteIds($websiteIds);
        $product->expects($this->never())
            ->method('setExtensionAttributes')
            ->with($this->extensionAttributesMock);
        $product->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($this->extensionAttributesMock);
        $this->assertEquals($this->readHandler->execute($product, []), $product);
    }
}
