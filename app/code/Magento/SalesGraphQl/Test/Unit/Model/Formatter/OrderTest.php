<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SalesGraphQl\Test\Unit\Model\Formatter;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\Intl\DateFormatterFactory;
use Magento\Framework\Stdlib\DateTime\Timezone;
use Magento\Sales\Model\Order as OrderModel;
use Magento\SalesGraphQl\Model\Formatter\Order;
use Magento\SalesGraphQl\Model\Order\OrderAddress;
use Magento\SalesGraphQl\Model\Order\OrderPayments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    /**
     * order_date is the UTC created_at value shown in the store timezone, whatever the store locale.
     *
     * created_at is an ISO "Y-m-d H:i:s" value. Passed to TimezoneInterface::date() as a string, it is
     * parsed with the locale's short pattern, which reads it as a different date in most locales
     * (e.g. "2026-10-13 11:27:08" became "18/04/2019 11:27:00" for cs_CZ).
     */
    #[DataProvider('localeDataProvider')]
    public function testOrderDateIsCreatedAtInStoreTimezone(string $locale): void
    {
        $order = $this->createStub(OrderModel::class);
        $order->method('getCreatedAt')->willReturn('2026-10-13 11:27:08');
        $order->method('getStatusHistories')->willReturn([]);

        $formatter = new Order(
            $this->createStub(OrderAddress::class),
            $this->createStub(OrderPayments::class),
            $this->createTimezone($locale, 'Europe/Prague')
        );

        $this->assertSame('13/10/2026 13:27:08', $formatter->format($order)['order_date']);
    }

    /**
     * @return array
     */
    public static function localeDataProvider(): array
    {
        return [
            'en_US' => ['en_US'],
            'cs_CZ' => ['cs_CZ'],
            'sl_SI' => ['sl_SI'],
            'de_DE' => ['de_DE'],
        ];
    }

    /**
     * Real Timezone with the store locale and timezone stubbed, so the locale-dependent parsing runs.
     *
     * @param string $locale
     * @param string $timezone
     * @return Timezone
     */
    private function createTimezone(string $locale, string $timezone): Timezone
    {
        $localeResolver = $this->createStub(ResolverInterface::class);
        $localeResolver->method('getLocale')->willReturn($locale);
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn($timezone);

        return new Timezone(
            $this->createStub(ScopeResolverInterface::class),
            $localeResolver,
            $this->createStub(DateTime::class),
            $scopeConfig,
            'store',
            'general/locale/timezone',
            new DateFormatterFactory()
        );
    }
}
