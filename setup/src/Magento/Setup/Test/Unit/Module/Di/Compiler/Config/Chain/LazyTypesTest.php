<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain;

use Magento\Setup\Module\Di\Compiler\Config\Chain\LazyTypes;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AFinalClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnAbstract;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnEnum;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnInterface;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AReadonlyClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ATrait;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ExtendsInternal;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\Foo\Proxy as FooProxy;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\MarkedNonLazy;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\PlainClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LazyTypesTest extends TestCase
{
    public function testEarlyReturnOnPhpBelow84(): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $this->markTestSkipped('Early-return path only triggers on PHP < 8.4.');
        }

        $input = ['arguments' => [PlainClass::class => []]];
        $output = (new LazyTypes())->modify($input);

        $this->assertSame($input, $output);
        $this->assertArrayNotHasKey('lazyTypes', $output);
    }

    public function testPlainClassIsAddedToAllowList(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new LazyTypes())->modify(
            ['arguments' => [PlainClass::class => []]]
        );

        $this->assertArrayHasKey('lazyTypes', $output);
        $this->assertArrayHasKey(PlainClass::class, $output['lazyTypes']);
    }

    #[DataProvider('disqualifiedTypesProvider')]
    public function testDisqualifiedTypeIsExcludedFromAllowList(string $class, string $reason): void
    {
        $this->skipIfPhpBelow84();

        $output = (new LazyTypes())->modify(
            ['arguments' => [$class => []]]
        );

        $this->assertArrayNotHasKey(
            $class,
            $output['lazyTypes'],
            "Expected $class to be excluded from the lazy allow-list because $reason"
        );
    }

    public static function disqualifiedTypesProvider(): array
    {
        return [
            'interface' => [AnInterface::class, 'interfaces cannot be lazy-instantiated'],
            'abstract class' => [AnAbstract::class, 'abstract classes cannot be lazy-instantiated'],
            'trait' => [ATrait::class, 'traits cannot be lazy-instantiated'],
            'final class' => [AFinalClass::class, 'final classes are not lazy-eligible'],
            'enum' => [AnEnum::class, 'enums are not lazy-eligible'],
            'readonly class' => [AReadonlyClass::class, 'readonly classes are not lazy-eligible'],
            'extends internal' => [ExtendsInternal::class, 'classes extending internal types are not lazy-eligible'],
            'Proxy suffix' => [FooProxy::class, 'classes with \\Proxy suffix are excluded'],
            'nonexistent class' => ['Some\\Nonexistent\\Type', 'unloadable classes cannot be lazy-instantiated'],
        ];
    }

    public function testCandidatesAggregateFromAllInputKeys(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new LazyTypes())->modify([
            'arguments' => [PlainClass::class => []],
            'instanceTypes' => ['VirtualType' => AFinalClass::class],
            'preferences' => ['SomeIface' => ExtendsInternal::class],
        ]);

        $this->assertArrayHasKey(PlainClass::class, $output['lazyTypes']);
        $this->assertArrayNotHasKey(AFinalClass::class, $output['lazyTypes']);
        $this->assertArrayNotHasKey(ExtendsInternal::class, $output['lazyTypes']);
        $this->assertArrayNotHasKey('VirtualType', $output['lazyTypes']);
    }

    public function testEmptyConfigProducesEmptyAllowList(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new LazyTypes())->modify([]);

        $this->assertSame([], $output['lazyTypes']);
    }

    /**
     * Classes opt out of lazy ghost construction by declaring the
     * Magento\Framework\ObjectManager\Attribute\NonLazy attribute. The compile-time scan
     * must exclude them from the allow-list even though they are otherwise PHP-compatible
     * (concrete, non-final, plain inheritance).
     */
    public function testClassWithNonLazyAttributeIsExcludedFromAllowList(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new LazyTypes())->modify(
            ['arguments' => [MarkedNonLazy::class => []]]
        );

        $this->assertArrayNotHasKey(MarkedNonLazy::class, $output['lazyTypes']);
    }

    /**
     * The chain step is one of several modifiers that can contribute to lazyTypes.
     * Pre-existing entries (from prior chain steps or upstream config sources) must be
     * preserved; the scanner only adds to the set, never replaces it.
     */
    public function testPreExistingLazyTypesArePreserved(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new LazyTypes())->modify([
            'arguments' => [PlainClass::class => []],
            'lazyTypes' => [
                'External\\PreSeeded\\Type' => true,
                PlainClass::class => true,
            ],
        ]);

        $this->assertArrayHasKey('External\\PreSeeded\\Type', $output['lazyTypes']);
        $this->assertArrayHasKey(PlainClass::class, $output['lazyTypes']);
    }

    private function skipIfPhpBelow84(): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('Lazy-eligibility scan only runs on PHP 8.4+.');
        }
    }
}
