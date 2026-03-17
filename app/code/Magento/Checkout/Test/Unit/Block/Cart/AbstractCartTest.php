<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Checkout\Test\Unit\Block\Cart;

use Magento\Backend\Block\Template\Context;
use Magento\Checkout\Block\Cart\AbstractCart;
use Magento\Checkout\Block\Cart\Item\Renderer as ItemRenderer;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\Element\RendererList;
use Magento\Framework\View\Layout;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Block\Items\AbstractItems;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

class AbstractCartTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    protected function setUp(): void
    {
        $this->_objectManager = new ObjectManager($this);
    }

    /**
     * @param string|null $type
     * @param string $expectedType
     */
    #[DataProvider('getItemRendererDataProvider')]
    public function testGetItemRenderer($type, $expectedType)
    {
        $renderer = $this->createMock(RendererList::class);

        $renderer->expects(
            $this->once()
        )->method(
            'getRenderer'
        )->with(
            $expectedType,
            AbstractCart::DEFAULT_TYPE
        )->willReturn(
            'rendererObject'
        );

        $layout = $this->createPartialMock(Layout::class, ['getChildName', 'getBlock']);

        $layout->expects($this->once())->method('getChildName')->willReturn('renderer.list');

        $layout->expects(
            $this->once()
        )->method(
            'getBlock'
        )->with(
            'renderer.list'
        )->willReturn(
            $renderer
        );

        /** @var AbstractItems $block */
        $block = $this->_objectManager->getObject(
            AbstractCart::class,
            [
                'context' => $this->_objectManager->getObject(
                    Context::class,
                    ['layout' => $layout]
                )
            ]
        );

        $this->assertSame('rendererObject', $block->getItemRenderer($type));
    }

    /**
     * @return array
     */
    public static function getItemRendererDataProvider()
    {
        return [[null, AbstractCart::DEFAULT_TYPE], ['some-type', 'some-type']];
    }

    public function testGetItemRendererThrowsExceptionForNonexistentRenderer()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Renderer list for block "" is not defined');
        $layout = $this->createPartialMock(Layout::class, ['getChildName', 'getBlock']);
        $layout->expects($this->once())->method('getChildName')->willReturn(null);

        /** @var \Magento\Checkout\Block\Cart\AbstractCart $block */
        $block = $this->_objectManager->getObject(
            AbstractCart::class,
            [
                'context' => $this->_objectManager->getObject(
                    Context::class,
                    ['layout' => $layout]
                )
            ]
        );

        $block->getItemRenderer('some-type');
    }

    public function testGetItemHtmlReturnsRendererHtml(): void
    {
        $expectedHtml = 'item html';

        $itemMock = $this->createMock(QuoteItem::class);
        $itemMock->expects($this->once())
            ->method('getProductType')
            ->willReturn('simple');

        $rendererBlockMock = $this->createPartialMock(ItemRenderer::class, ['setItem', 'toHtml']);
        $rendererBlockMock->expects($this->once())
            ->method('setItem')
            ->with($itemMock)
            ->willReturnSelf();
        $rendererBlockMock->expects($this->once())
            ->method('toHtml')
            ->willReturn($expectedHtml);

        $rendererListMock = $this->createMock(RendererList::class);
        $getRendererArgs = [];
        $rendererListMock->expects($this->once())
            ->method('getRenderer')
            ->willReturnCallback(function (...$args) use (&$getRendererArgs, $rendererBlockMock) {
                $getRendererArgs = $args;
                return $rendererBlockMock;
            });

        $layout = $this->createPartialMock(Layout::class, ['getChildName', 'getBlock']);
        $layout->expects($this->once())->method('getChildName')->willReturn('renderer.list');
        $layout->expects($this->once())
            ->method('getBlock')
            ->with('renderer.list')
            ->willReturn($rendererListMock);

        /** @var AbstractCart $block */
        $block = $this->_objectManager->getObject(
            AbstractCart::class,
            [
                'context' => $this->_objectManager->getObject(
                    Context::class,
                    ['layout' => $layout]
                )
            ]
        );

        $this->assertSame($expectedHtml, $block->getItemHtml($itemMock));
        $this->assertSame('simple', $getRendererArgs[0] ?? null);
        $this->assertSame(AbstractCart::DEFAULT_TYPE, $getRendererArgs[1] ?? null);
    }

    /**
     * @param array $expectedResult
     * @param bool $isVirtual
     */
    #[DataProvider('getTotalsCacheDataProvider')]
    public function testGetTotalsCache($expectedResult, $isVirtual)
    {
        $totals = $isVirtual ? ['billing_totals'] : ['shipping_totals'];
        $addressMock = $this->createMock(Address::class);
        $checkoutSessionMock = $this->createMock(Session::class);
        $quoteMock = $this->createMock(Quote::class);
        $checkoutSessionMock->expects($this->once())->method('getQuote')->willReturn($quoteMock);

        $quoteMock->expects($this->once())->method('isVirtual')->willReturn($isVirtual);
        $quoteMock->method('getShippingAddress')->willReturn($addressMock);
        $quoteMock->method('getBillingAddress')->willReturn($addressMock);
        $addressMock->expects($this->once())->method('getTotals')->willReturn($totals);

        /** @var \Magento\Checkout\Block\Cart\AbstractCart $model */
        $model = $this->_objectManager->getObject(
            AbstractCart::class,
            ['checkoutSession' => $checkoutSessionMock]
        );
        $this->assertEquals($expectedResult, $model->getTotalsCache());
    }

    /**
     * @return array
     */
    public static function getTotalsCacheDataProvider()
    {
        return [
            [['billing_totals'], true],
            [['shipping_totals'], false]
        ];
    }

    public function testGetQuoteRecollectsTotalsOnceForPersistedQuote(): void
    {
        $checkoutSessionMock = $this->createMock(Session::class);
        $quoteMock = $this->createPartialMock(
            Quote::class,
            [
                'getId',
                'getItemsCount',
                'getItemsQty',
                'getData',
                'collectTotals',
                'setItemsCount',
                'setItemsQty',
            ]
        );
        $scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $checkoutSessionMock->expects($this->once())
            ->method('getData')
            ->with('last_cart_totals_recollect_at')
            ->willReturn(0);

        $quoteMock->expects($this->once())->method('getId')->willReturn(123);
        $quoteMock->method('getItemsCount')->willReturn(2);
        $quoteMock->method('getItemsQty')->willReturn(2.0);
        $quoteMock->method('getData')->willReturnMap([['virtual_items_qty', null, 0]]);
        $quoteMock->expects($this->once())->method('collectTotals')->willReturnSelf();
        $quoteMock->expects($this->once())->method('setItemsCount')->with(2)->willReturnSelf();
        $quoteMock->expects($this->once())->method('setItemsQty')->with(2.0)->willReturnSelf();

        $scopeConfigMock->expects($this->never())->method('getValue');

        /** @var AbstractCart $model */
        $model = $this->_objectManager->getObject(
            AbstractCart::class,
            [
                'checkoutSession' => $checkoutSessionMock,
                'scopeConfig' => $scopeConfigMock,
            ]
        );

        $this->assertSame($quoteMock, $model->getQuote());
        $this->assertSame($quoteMock, $model->getQuote());
    }

    public function testGetQuoteDoesNotRecollectTotalsWhenQuoteIsNotPersisted(): void
    {
        $checkoutSessionMock = $this->createMock(Session::class);
        $quoteMock = $this->createPartialMock(
            Quote::class,
            ['getId', 'getData', 'setData', 'collectTotals']
        );

        $checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);

        $quoteMock->expects($this->once())
            ->method('getId')
            ->willReturn(null);
        $quoteMock->expects($this->never())->method('getData');
        $quoteMock->expects($this->never())->method('setData');
        $quoteMock->expects($this->never())->method('collectTotals');

        /** @var AbstractCart $model */
        $model = $this->_objectManager->getObject(
            AbstractCart::class,
            ['checkoutSession' => $checkoutSessionMock]
        );

        $this->assertSame($quoteMock, $model->getQuote());
    }

    public function testGetQuoteDoesNotRecollectTotalsWhenAlreadyRecollected(): void
    {
        $checkoutSessionMock = $this->createMock(Session::class);
        $quoteMock = $this->createPartialMock(Quote::class, ['getId', 'collectTotals']);
        $scopeConfigMock = $this->createMock(ScopeConfigInterface::class);

        $checkoutSessionMock->expects($this->once())
            ->method('getQuote')
            ->willReturn($quoteMock);
        $checkoutSessionMock->expects($this->once())
            ->method('getData')
            ->with('last_cart_totals_recollect_at')
            ->willReturn(2000);

        $quoteMock->expects($this->once())->method('getId')->willReturn(123);
        $quoteMock->expects($this->never())->method('collectTotals');

        $scopeConfigMock->expects($this->once())
            ->method('getValue')
            ->with(\Magento\Checkout\Observer\CatalogRuleSaveAfterObserver::CONFIG_PATH_CATALOG_RULES_UPDATED_VERSION)
            ->willReturn('1000');

        /** @var AbstractCart $model */
        $model = $this->_objectManager->getObject(
            AbstractCart::class,
            [
                'checkoutSession' => $checkoutSessionMock,
                'scopeConfig' => $scopeConfigMock,
            ]
        );

        $this->assertSame($quoteMock, $model->getQuote());
    }
}
