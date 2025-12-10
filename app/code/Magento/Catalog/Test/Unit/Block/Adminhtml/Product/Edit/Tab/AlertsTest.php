<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Block\Adminhtml\Product\Edit\Tab;

use Magento\Backend\Block\Widget\Accordion;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Price;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Alerts\Stock;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AlertsTest extends TestCase
{
    /**
     * @var Alerts
     */
    protected $alerts;

    /**
     * @var MockObject
     */
    protected $scopeConfigMock;

    /**
     * @var LayoutInterface&MockObject
     */
    protected $layoutMock;

    protected function setUp(): void
    {
        $helper = new ObjectManager($this);
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);
        $this->layoutMock = $this->createMock(LayoutInterface::class);

        $this->alerts = $helper->getObject(
            Alerts::class,
            ['scopeConfig' => $this->scopeConfigMock]
        );
    }

    /**
     * Helper method to set up layout mock with accordion and alert blocks
     *
     * @param bool $includePriceBlock
     * @param bool $includeStockBlock
     * @return void
     */
    private function setupLayoutMock($includePriceBlock = false, $includeStockBlock = false): void
    {
        $accordionMock = $this->getMockBuilder(Accordion::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['addItem'])
            ->addMethods(['setId'])
            ->getMock();

        // Allow setId to be called any number of times (may be called during layout setup)
        $accordionMock->method('setId')
            ->with('productAlerts')
            ->willReturnSelf();
        $blockMap = [
            [Accordion::class, '', [], $accordionMock]
        ];

        if ($includePriceBlock) {
            $priceBlockMock = $this->getMockBuilder(Price::class)
                ->disableOriginalConstructor()
                ->onlyMethods(['toHtml'])
                ->getMock();
            $priceBlockMock->method('toHtml')->willReturn('<price-content>');
            $blockMap[] = [Price::class, '', [], $priceBlockMock];
        }

        if ($includeStockBlock) {
            $stockBlockMock = $this->createMock(Stock::class);
            $blockMap[] = [Stock::class, '', [], $stockBlockMock];
        }

        $this->layoutMock->method('createBlock')
            ->willReturnMap($blockMap);
        $this->alerts->setLayout($this->layoutMock);
    }

    /**
     * @param bool $priceAllow
     * @param bool $stockAllow
     * @param bool $canShowTab
     *
     * @dataProvider canShowTabDataProvider
     */
    public function testCanShowTab($priceAllow, $stockAllow, $canShowTab)
    {
        $valueMap = [
            [
                'catalog/productalert/allow_price',
                ScopeInterface::SCOPE_STORE,
                null,
                $priceAllow,
            ],
            [
                'catalog/productalert/allow_stock',
                ScopeInterface::SCOPE_STORE,
                null,
                $stockAllow
            ],
        ];
        $this->scopeConfigMock->expects($this->any())->method('getValue')->willReturnMap($valueMap);
        $this->assertEquals($canShowTab, $this->alerts->canShowTab());
    }

    /**
     * Test prepareLayout with different alert configurations
     *
     * @dataProvider alertConfigurationProvider
     * @param bool $priceAllow
     * @param bool $stockAllow
     * @return void
     */
    public function testPrepareLayout($priceAllow, $stockAllow): void
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnCallback(function ($path) use ($priceAllow, $stockAllow) {
                if ($path === 'catalog/productalert/allow_price') {
                    return $priceAllow;
                }
                if ($path === 'catalog/productalert/allow_stock') {
                    return $stockAllow;
                }
                return false;
            });

        $this->setupLayoutMock($priceAllow, $stockAllow);

        $reflection = new \ReflectionClass($this->alerts);
        $method = $reflection->getMethod('_prepareLayout');
        $method->setAccessible(true);
        $method->invoke($this->alerts);

        // Get accordion HTML to verify child was set
        $result = $this->alerts->getAccordionHtml();
        $this->assertNotNull($result);
    }

    /**
     * Data provider for alert configuration scenarios
     *
     * Tests all possible combinations of price and stock alert configurations
     *
     * @return array
     */
    public static function alertConfigurationProvider(): array
    {
        return [
            'both_enabled' => [true, true],
            'only_price' => [true, false],
            'only_stock' => [false, true],
            'both_disabled' => [false, false],
        ];
    }

    /**
     * @return array
     */
    public static function canShowTabDataProvider()
    {
        return [
            'alert_price_and_stock_allow' => [true, true, true],
            'alert_price_is_allowed_and_stock_is_unallowed' => [true, false, true],
            'alert_price_is_unallowed_and_stock_is_allowed' => [false, true, true],
            'alert_price_is_unallowed_and_stock_is_unallowed' => [false, false, false]
        ];
    }
}
