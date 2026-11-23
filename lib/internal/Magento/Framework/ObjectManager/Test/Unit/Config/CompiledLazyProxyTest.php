<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager\Test\Unit\Config;

use Magento\Framework\ObjectManager\Config\Compiled;
use Magento\Framework\ObjectManager\LazyProxyAwareInterface;
use PHPUnit\Framework\TestCase;

/**
 * Verifies storage and area merging of the compiled native proxy map.
 */
class CompiledLazyProxyTest extends TestCase
{
    /**
     * Absent compile data must never opt a proxy into native construction.
     */
    public function testMissingCompileDataReturnsNull(): void
    {
        $config = new Compiled([]);

        self::assertInstanceOf(LazyProxyAwareInterface::class, $config);
        self::assertNull($config->getLazyProxyTarget('Vendor\\Subject\\Proxy'));
    }

    /**
     * Merge per proxy name, retaining unrelated targets and replacing area-specific targets.
     */
    public function testProxyTargetsMergeByType(): void
    {
        $config = new Compiled(['lazyProxies' => [
            'Vendor\\Subject\\Proxy' => 'Vendor\\Subject',
            'Vendor\\Other\\Proxy' => 'Vendor\\Other',
        ]]);
        self::assertSame('Vendor\\Subject', $config->getLazyProxyTarget('Vendor\\Subject\\Proxy'));

        $config->extend(['lazyProxies' => [
            'Vendor\\Subject\\Proxy' => 'Vendor\\Subject\\Interceptor',
            'Vendor\\NewSubject\\Proxy' => 'Vendor\\NewSubject',
        ]]);

        self::assertContains('lazyProxies', Compiled::MERGED_SECTIONS);
        self::assertSame('Vendor\\Subject\\Interceptor', $config->getLazyProxyTarget('Vendor\\Subject\\Proxy'));
        self::assertSame('Vendor\\Other', $config->getLazyProxyTarget('Vendor\\Other\\Proxy'));
        self::assertSame('Vendor\\NewSubject', $config->getLazyProxyTarget('Vendor\\NewSubject\\Proxy'));
        self::assertNull($config->getLazyProxyTarget('Vendor\\Unknown\\Proxy'));
    }
}
