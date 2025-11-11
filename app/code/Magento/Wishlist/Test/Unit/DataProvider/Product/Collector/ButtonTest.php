<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Wishlist\Test\Unit\DataProvider\Product\Collector;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductRender\ButtonInterface;
use Magento\Catalog\Api\Data\ProductRender\ButtonInterfaceFactory;
use Magento\Catalog\Api\Data\ProductRenderExtensionFactory;
use Magento\Catalog\Api\Data\ProductRenderExtensionInterface;
use Magento\Catalog\Api\Data\ProductRenderInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Ui\DataProvider\Product\Collector\Button;
use Magento\Framework\TestFramework\Unit\Helper\MockCreationTrait;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Collect information needed to render wishlist button on front
 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
 */
class ButtonTest extends TestCase
{
    use MockCreationTrait;

    /** @var Button */
    private $button;

    /** @var ProductRenderExtensionFactory|MockObject */
    private $productRenderExtensionFactoryMock;

    /** @var Data|MockObject */
    private $wishlistHelperMock;

    /** @var ButtonInterfaceFactory|MockObject */
    private $buttonInterfaceFactoryMock;

    protected function setUp(): void
    {
        $this->productRenderExtensionFactoryMock = $this->createMock(
            ProductRenderExtensionFactory::class
        );
        $this->buttonInterfaceFactoryMock = $this->createMock(
            ButtonInterfaceFactory::class
        );
        $this->wishlistHelperMock = $this->createMock(Data::class);

        $this->button = new Button(
            $this->wishlistHelperMock,
            $this->productRenderExtensionFactoryMock,
            $this->buttonInterfaceFactoryMock
        );
    }

    public function testCollect()
    {
        $productRendererMock = $this->createMock(ProductRenderInterface::class);
        $productMock = $this->createMock(ProductInterface::class);
        $wishlistButton = null;
        /** @var ProductRenderExtensionInterface $productRendererExtensionMock */
        $productRendererExtensionMock = $this->createPartialMockWithReflection(
            ProductRenderExtensionInterface::class,
            ['getWishlistButton', 'setWishlistButton']
        );
        $buttonInterfaceMock = $this->createMock(ButtonInterface::class);

        $productRendererMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn($productRendererExtensionMock);
        $this->buttonInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($buttonInterfaceMock);
        $this->wishlistHelperMock->expects($this->once())
            ->method('getAddParams')
            ->with($productMock)
            ->willReturn('http://www.example.com/');
        $buttonInterfaceMock->expects($this->once())
            ->method('setUrl')
            ->with('http://www.example.com/');

        $storedButton = null;
        $productRendererExtensionMock->method('setWishlistButton')
            ->willReturnCallback(function ($button) use (&$storedButton) {
                $storedButton = $button;
                return null;
            });
        $productRendererExtensionMock->method('getWishlistButton')
            ->willReturnCallback(function () use (&$storedButton) {
                return $storedButton;
            });
        
        $productRendererExtensionMock->setWishlistButton($buttonInterfaceMock);
        $this->assertEquals($buttonInterfaceMock, $productRendererExtensionMock->getWishlistButton());

        $this->button->collect($productMock, $productRendererMock);
    }

    public function testCollectEmptyExtensionAttributes()
    {
        $productRendererMock = $this->createMock(ProductRenderInterface::class);
        $productMock = $this->createMock(ProductInterface::class);
        $buttonInterfaceMock = $this->createMock(ButtonInterface::class);
        $wishlistButton = null;
        /** @var ProductRenderExtensionInterface $productRendererExtensionMock */
        $productRendererExtensionMock = $this->createPartialMockWithReflection(
            ProductRenderExtensionInterface::class,
            ['setWishlistButton', 'getWishlistButton']
        );

        $productRendererMock->expects($this->once())
            ->method('getExtensionAttributes')
            ->willReturn('');
        $this->productRenderExtensionFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($productRendererExtensionMock);
        $this->buttonInterfaceFactoryMock->expects($this->once())
            ->method('create')
            ->willReturn($buttonInterfaceMock);
        $this->wishlistHelperMock->expects($this->once())
            ->method('getAddParams')
            ->with($productMock)
            ->willReturn('http://www.example.com/');
        $buttonInterfaceMock->expects($this->once())
            ->method('setUrl')
            ->with('http://www.example.com/');

        $storedButton = null;
        $productRendererExtensionMock->method('setWishlistButton')
            ->willReturnCallback(function ($button) use (&$storedButton) {
                $storedButton = $button;
                return null;
            });
        $productRendererExtensionMock->method('getWishlistButton')
            ->willReturnCallback(function () use (&$storedButton) {
                return $storedButton;
            });
        
        $productRendererExtensionMock->setWishlistButton($buttonInterfaceMock);
        $this->assertEquals($buttonInterfaceMock, $productRendererExtensionMock->getWishlistButton());

        $this->button->collect($productMock, $productRendererMock);
    }
}
