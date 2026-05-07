<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain;

use Magento\Setup\Module\Di\Compiler\Config\Chain\NonLazyTypes;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\AFinalClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\AnAbstract;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\AnEnum;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\AnInterface;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\AReadonlyClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\ATrait;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\ExtendsInternal;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\Foo\Proxy as FooProxy;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\MarkedNonLazy;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\NonLazyTypes\PlainClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NonLazyTypesTest extends TestCase
{
    public function testEarlyReturnOnPhpBelow84(): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $this->markTestSkipped('Early-return path only triggers on PHP < 8.4.');
        }

        $input = ['arguments' => [PlainClass::class => []]];
        $output = (new NonLazyTypes())->modify($input);

        $this->assertSame($input, $output);
        $this->assertArrayNotHasKey('nonLazyTypes', $output);
    }

    public function testPlainClassIsLazyEligible(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new NonLazyTypes())->modify(
            ['arguments' => [PlainClass::class => []]]
        );

        $this->assertArrayHasKey('nonLazyTypes', $output);
        $this->assertArrayNotHasKey(PlainClass::class, $output['nonLazyTypes']);
    }

    #[DataProvider('disqualifiedTypesProvider')]
    public function testDisqualifiedTypeAppearsInDenyList(string $class, string $reason): void
    {
        $this->skipIfPhpBelow84();

        $output = (new NonLazyTypes())->modify(
            ['arguments' => [$class => []]]
        );

        $this->assertArrayHasKey(
            $class,
            $output['nonLazyTypes'],
            "Expected $class to be flagged non-lazy because $reason"
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

        $output = (new NonLazyTypes())->modify([
            'arguments' => [AnInterface::class => []],
            'instanceTypes' => ['VirtualType' => AnAbstract::class],
            'preferences' => ['SomeIface' => ATrait::class],
        ]);

        $this->assertArrayHasKey(AnInterface::class, $output['nonLazyTypes']);
        $this->assertArrayHasKey(AnAbstract::class, $output['nonLazyTypes']);
        $this->assertArrayHasKey(ATrait::class, $output['nonLazyTypes']);
    }

    public function testEmptyConfigProducesEmptyDenyList(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new NonLazyTypes())->modify([]);

        $this->assertSame([], $output['nonLazyTypes']);
    }

    /**
     * Classes opt out of lazy ghost construction by declaring the
     * Magento\Framework\ObjectManager\Attribute\NonLazy attribute. The compile-time scan
     * must surface them in the deny-list even though they are otherwise PHP-compatible
     * (concrete, non-final, plain inheritance).
     */
    public function testClassWithNonLazyAttributeIsAddedToDenyList(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new NonLazyTypes())->modify(
            ['arguments' => [MarkedNonLazy::class => []]]
        );

        $this->assertArrayHasKey(MarkedNonLazy::class, $output['nonLazyTypes']);
    }

    /**
     * The chain step is one of several modifiers that can contribute to nonLazyTypes.
     * Pre-existing entries (from prior chain steps or upstream config sources) must be
     * preserved; the scanner only adds to the set, never replaces it.
     */
    public function testPreExistingNonLazyTypesArePreserved(): void
    {
        $this->skipIfPhpBelow84();

        $output = (new NonLazyTypes())->modify([
            'arguments' => [AnInterface::class => []],
            'nonLazyTypes' => [
                'External\\PreSeeded\\Type' => true,
                AnInterface::class => true,
            ],
        ]);

        $this->assertArrayHasKey('External\\PreSeeded\\Type', $output['nonLazyTypes']);
        $this->assertArrayHasKey(AnInterface::class, $output['nonLazyTypes']);
    }

    private function skipIfPhpBelow84(): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('Lazy-eligibility scan only runs on PHP 8.4+.');
        }
    }
}
