<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Model\Dashboard;

use Magento\Backend\Model\Dashboard\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    #[DataProvider('lifetimeProvider')]
    public function testLifetimesAreNonNegativeIntegers($raw, int $expected): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn($raw);
        $config = new Config($scopeConfig);

        $this->assertSame($expected, $config->getLifetimeCacheLifetime());
        $this->assertSame($expected, $config->getTotalsCacheLifetime());
    }

    public static function lifetimeProvider(): array
    {
        return [
            'default' => ['3600', 3600],
            'zero' => ['0', 0],
            'null' => [null, 0],
            'negative' => ['-5', 0],
            'garbage' => ['abc', 0],
        ];
    }

    #[DataProvider('lastOrdersProvider')]
    public function testLastOrdersCountIsClamped($raw, int $expected): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->with(Config::XML_PATH_LAST_ORDERS_COUNT)->willReturn($raw);

        $this->assertSame($expected, (new Config($scopeConfig))->getLastOrdersCount());
    }

    public static function lastOrdersProvider(): array
    {
        return [
            'default' => ['5', 5],
            'custom' => ['12', 12],
            'above max' => ['50', 20],
            'zero falls back' => ['0', 5],
            'missing falls back' => [null, 5],
        ];
    }

    public function testIsChartsEnabled(): void
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with(Config::XML_PATH_ENABLE_CHARTS)
            ->willReturn(true);

        $this->assertTrue((new Config($scopeConfig))->isChartsEnabled());
    }
}
