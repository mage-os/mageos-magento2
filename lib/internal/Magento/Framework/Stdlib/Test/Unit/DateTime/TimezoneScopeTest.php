<?php
declare(strict_types=1);
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */

namespace Magento\Framework\Stdlib\Test\Unit\DateTime;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ScopeInterface;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Framework\Stdlib\DateTime\Intl\DateFormatterFactory;
use Magento\Framework\Stdlib\DateTime\Timezone;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Test for scope date interval functionality @see Timezone
 */
class TimezoneScopeTest extends TestCase
{
    /**
     * @var string|null
     */
    private ?string $defaultTimeZone;

    /**
     * @var string
     */
    private string $scopeType = 'store';

    /**
     * @var string
     */
    private string $defaultTimezonePath = 'default/timezone/path';

    /**
     * @var ScopeResolverInterface|MockObject
     */
    private ScopeResolverInterface|MockObject $scopeResolver;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private MockObject|ScopeConfigInterface $scopeConfig;

    /**
     * @var ResolverInterface|MockObject
     */
    private ResolverInterface|MockObject $localeResolver;

    /**
     * @inheritdoc
     */
    protected function setUp(): void
    {
        $this->defaultTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $this->scopeType = 'store';
        $this->defaultTimezonePath = 'default/timezone/path';

        $this->scopeResolver = $this->getMockBuilder(ScopeResolverInterface::class)
            ->getMock();
        $this->localeResolver = $this->getMockBuilder(ResolverInterface::class)
            ->getMock();
        $this->scopeConfig = $this->getMockBuilder(ScopeConfigInterface::class)
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown(): void
    {
        date_default_timezone_set($this->defaultTimeZone);
    }

    /**
     * @return Timezone
     * @throws Exception
     */
    private function getTimezone(): Timezone
    {
        return new Timezone(
            $this->scopeResolver,
            $this->localeResolver,
            $this->createMock(DateTime::class),
            $this->scopeConfig,
            $this->scopeType,
            $this->defaultTimezonePath,
            new DateFormatterFactory()
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testIsScopeDateInInterval()
    {
        $scopeMock = $this->createMock(ScopeInterface::class);
        $this->scopeResolver->method('getScope')->willReturn($scopeMock);

        $result = $this->getTimezone()->isScopeDateInInterval(
            null,
            '2025-04-01 00:00:00',
            '2999-05-01 00:00:00',
        );

        $this->assertTrue($result);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testIsScopeDateInIntervalFalse()
    {
        $scopeMock = $this->createMock(ScopeInterface::class);
        $this->scopeResolver->method('getScope')->willReturn($scopeMock);

        $result = $this->getTimezone()->isScopeDateInInterval(
            null,
            '2025-03-01 00:00:00',
            '2025-04-01 00:00:00',
        );

        $this->assertFalse($result);
    }
}
