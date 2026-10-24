<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain;

use Magento\Setup\Module\Di\Compiler\Config\Chain\LazyTypes;
use Magento\Setup\Module\Di\Compiler\ConstructorSideEffectScanner;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AFinalClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnAbstract;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnEnum;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnInterface;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AReadonlyClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ATrait;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\CoreParentWithHook;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ExtendsInternal;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ExtendsNonLazyTraitUser;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\Foo\Proxy as FooProxy;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ExtendsMarkedNonLazy;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ImplementsNonLazyInterface;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\InheritsThirdPartyConstructor;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\InheritsTrivialThirdPartyConstructor;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\MarkedNonLazy;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\PlainClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\UsesNestedNonLazyTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LazyTypesTest extends TestCase
{
    private const FIXTURE_NAMESPACE = 'Vendor\\LazyTypesFixture\\';

    public static function setUpBeforeClass(): void
    {
        // Fixtures outside the Magento\ namespace are not covered by the test autoloader
        spl_autoload_register(static function (string $class): void {
            foreach ([self::FIXTURE_NAMESPACE, 'MageOS\\LazyTypesFixture\\'] as $namespace) {
                $file = __DIR__ . '/_files/LazyTypes/ThirdParty/' . substr($class, strlen($namespace)) . '.php';
                if (str_starts_with($class, $namespace) && is_file($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }

    public function testEarlyReturnOnPhpBelow84(): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $this->markTestSkipped('Early-return path only triggers on PHP < 8.4.');
        }

        $input = ['arguments' => [PlainClass::class => []]];
        $output = $this->createLazyTypes()->modify($input);

        $this->assertSame($input, $output);
        $this->assertArrayNotHasKey('lazyTypes', $output);
    }

    public function testPlainClassIsAddedToAllowList(): void
    {
        $this->skipIfPhpBelow84();

        $output = $this->createLazyTypes()->modify(
            ['arguments' => [PlainClass::class => []]]
        );

        $this->assertArrayHasKey('lazyTypes', $output);
        $this->assertArrayHasKey(PlainClass::class, $output['lazyTypes']);
    }

    #[DataProvider('disqualifiedTypesProvider')]
    public function testDisqualifiedTypeIsExcludedFromAllowList(string $class, string $reason): void
    {
        $this->skipIfPhpBelow84();

        $output = $this->createLazyTypes()->modify(
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

        $output = $this->createLazyTypes()->modify([
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

        $output = $this->createLazyTypes()->modify([]);

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

        $output = $this->createLazyTypes()->modify(
            ['arguments' => [MarkedNonLazy::class => []]]
        );

        $this->assertArrayNotHasKey(MarkedNonLazy::class, $output['lazyTypes']);
    }

    /**
     * PHP attributes are not inherited, so the scan must walk ancestors: a subclass of a
     * #[NonLazy] class (e.g. Customer\Model\Session extending SessionManager) stays eager.
     */
    public function testSubclassOfNonLazyClassIsExcludedFromAllowList(): void
    {
        $this->skipIfPhpBelow84();

        $output = $this->createLazyTypes()->modify(
            ['arguments' => [ExtendsMarkedNonLazy::class => []]]
        );

        $this->assertArrayNotHasKey(ExtendsMarkedNonLazy::class, $output['lazyTypes']);
    }

    #[DataProvider('nonLazyViaInterfaceOrTraitProvider')]
    public function testNonLazyOnInterfaceOrTraitExcludesClass(string $class): void
    {
        $this->skipIfPhpBelow84();

        $output = $this->createLazyTypes()->modify(['arguments' => [$class => []]]);

        $this->assertArrayNotHasKey($class, $output['lazyTypes']);
    }

    public static function nonLazyViaInterfaceOrTraitProvider(): array
    {
        return [
            'implements #[NonLazy] interface' => [ImplementsNonLazyInterface::class],
            'uses trait that uses #[NonLazy] trait' => [UsesNestedNonLazyTrait::class],
            'extends class using #[NonLazy] trait' => [ExtendsNonLazyTraitUser::class],
        ];
    }

    /**
     * The chain step is one of several modifiers that can contribute to lazyTypes.
     * Pre-existing entries (from prior chain steps or upstream config sources) must be
     * preserved; the scanner only adds to the set, never replaces it.
     */
    public function testPreExistingLazyTypesArePreserved(): void
    {
        $this->skipIfPhpBelow84();

        $output = $this->createLazyTypes()->modify([
            'arguments' => [PlainClass::class => []],
            'lazyTypes' => [
                'External\\PreSeeded\\Type' => true,
                PlainClass::class => true,
            ],
        ]);

        $this->assertArrayHasKey('External\\PreSeeded\\Type', $output['lazyTypes']);
        $this->assertArrayHasKey(PlainClass::class, $output['lazyTypes']);
    }

    /**
     * Third-party classes are lazy only when their constructor is provably side-effect-free (#353).
     */
    #[DataProvider('thirdPartyProvider')]
    public function testThirdPartyClassIsLazyOnlyWhenConstructorIsSideEffectFree(string $fixture, bool $lazy): void
    {
        $this->skipIfPhpBelow84();

        $class = self::FIXTURE_NAMESPACE . $fixture;
        $output = $this->createLazyTypes()->modify(['arguments' => [$class => []]]);

        $this->assertSame($lazy, isset($output['lazyTypes'][$class]));
    }

    public static function thirdPartyProvider(): array
    {
        return [
            'empty constructor' => ['PlainThirdParty', true],
            'trivial property assignments' => ['TrivialAssigns', true],
            'promoted properties' => ['PromotedProperties', true],
            'no constructor' => ['NoConstructor', true],
            'inherited trivial constructor' => ['InheritsTrivialConstructor', true],
            'trait-provided trivial constructor' => ['TraitConstructor', true],
            'ObjectManager BC fallback' => ['ObjectManagerFallback', true],
            'parent::__construct to trivial parent' => ['ChildOfTrivialParent', true],
            'extends core without _construct' => ['ExtendsCoreWithoutHook', true],
            'define() calls (Fooman-style)' => ['DefinesConstants', false],
            'init method call (Braintree-style)' => ['CallsInitMethod', false],
            'if in constructor' => ['ConditionalConstructor', false],
            'new in default parameter' => ['NewInDefaultParameter', false],
            'parent::__construct to non-trivial parent' => ['ChildOfSideEffectParent', false],
            'extends core and overrides _construct' => ['ExtendsCoreOverridingHook', false],
            'extends core and overrides another method its constructor calls' => [
                'ExtendsCoreOverridingInitSelect',
                false,
            ],
            '#[NonLazy] with trivial constructor' => ['NonLazyThirdParty', false],
        ];
    }

    /**
     * Magento\ and MageOS\ classes stay lazy by default without a constructor scan.
     */
    public function testCoreClassesAreLazyRegardlessOfConstructor(): void
    {
        $this->skipIfPhpBelow84();

        $output = $this->createLazyTypes()->modify([
            'arguments' => [
                'MageOS\\LazyTypesFixture\\MageOSClass' => [],
                CoreParentWithHook::class => [],
            ],
        ]);

        $this->assertArrayHasKey('MageOS\\LazyTypesFixture\\MageOSClass', $output['lazyTypes']);
        $this->assertArrayHasKey(CoreParentWithHook::class, $output['lazyTypes']);
    }

    /**
     * Trust follows the class that declares the constructor, so a core class inheriting a
     * third-party constructor is scanned.
     */
    public function testCoreClassInheritingThirdPartyConstructorIsScanned(): void
    {
        $this->skipIfPhpBelow84();

        $output = $this->createLazyTypes()->modify([
            'arguments' => [
                InheritsThirdPartyConstructor::class => [],
                InheritsTrivialThirdPartyConstructor::class => [],
            ],
        ]);

        $this->assertArrayNotHasKey(InheritsThirdPartyConstructor::class, $output['lazyTypes']);
        $this->assertArrayHasKey(InheritsTrivialThirdPartyConstructor::class, $output['lazyTypes']);
    }

    private function createLazyTypes(): LazyTypes
    {
        return new LazyTypes(new ConstructorSideEffectScanner());
    }

    private function skipIfPhpBelow84(): void
    {
        if (PHP_VERSION_ID < 80400) {
            $this->markTestSkipped('Lazy-eligibility scan only runs on PHP 8.4+.');
        }
    }
}
