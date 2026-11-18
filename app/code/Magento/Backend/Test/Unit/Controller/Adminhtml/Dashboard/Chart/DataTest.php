<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Controller\Adminhtml\Dashboard\Chart;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Controller\Adminhtml\Dashboard\Chart\Data;
use Magento\Backend\Model\Dashboard\Chart;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public function testExecuteReturnsBothSeriesWithScopeCurrencyAndLocale(): void
    {
        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->willReturnCallback(
            fn ($name) => ['period' => '7d', 'store' => '2', 'website' => null, 'group' => null][$name] ?? null
        );
        $context = $this->createMock(Context::class);
        $context->method('getRequest')->willReturn($request);

        $chart = $this->createMock(Chart::class);
        $chart->expects($this->once())
            ->method('getSeriesByPeriod')
            ->with('7d', '2', null, null)
            ->willReturn([
                'period' => '7d',
                'quantity' => [['x' => '2026-09-21', 'y' => 2.0]],
                'revenue' => [['x' => '2026-09-21', 'y' => 421.0]],
            ]);

        $store = $this->createMock(Store::class);
        $store->method('getBaseCurrencyCode')->willReturn('EUR');
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->expects($this->once())->method('getStore')->with('2')->willReturn($store);

        $localeResolver = $this->createMock(ResolverInterface::class);
        $localeResolver->method('getLocale')->willReturn('de_DE');

        $captured = null;
        $json = $this->createMock(Json::class);
        $json->method('setData')->willReturnCallback(function ($data) use (&$captured, $json) {
            $captured = $data;
            return $json;
        });
        $jsonFactory = $this->createMock(JsonFactory::class);
        $jsonFactory->method('create')->willReturn($json);

        $controller = (new ObjectManager($this))->getObject(Data::class, [
            'context' => $context,
            'resultJsonFactory' => $jsonFactory,
            'chart' => $chart,
            'storeManager' => $storeManager,
            'localeResolver' => $localeResolver,
        ]);

        $this->assertSame($json, $controller->execute());
        $this->assertSame('7d', $captured['period']);
        $this->assertSame('EUR', $captured['currency']);
        $this->assertSame('de-DE', $captured['locale']);
        $this->assertSame('Orders', (string)$captured['series']['orders']['label']);
        $this->assertSame([['x' => '2026-09-21', 'y' => 2.0]], $captured['series']['orders']['data']);
        $this->assertSame('Revenue', (string)$captured['series']['amounts']['label']);
        $this->assertSame([['x' => '2026-09-21', 'y' => 421.0]], $captured['series']['amounts']['data']);
    }
}
