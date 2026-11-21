<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Setup\Module\Di\Compiler\Config\Chain\LazyProxies;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\ExtendsNonLazySubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\NonLazyInterfaceSubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\NonLazySubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\NonLazyTraitSubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\NullableProxyConsumer;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\ProxyConsumer;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\ResettableSubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\StatelessSubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\StaticOnlySubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\Subject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\Subject\Interceptor;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\UnionProxyConsumer;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyProxies\WrittenSubject;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AFinalClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnAbstract;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnEnum;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AnInterface;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\AReadonlyClass;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ATrait;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\ExtendsInternal;
use Magento\Setup\Test\Unit\Module\Di\Compiler\Config\Chain\_files\LazyTypes\PlainClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\TestCase;

/**
 * Verifies compile-time native proxy eligibility and candidate discovery.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LazyProxiesTest extends TestCase
{
    /**
     * Preserve compiled configuration on runtimes without native lazy objects.
     */
    public function testPhpBelow84ReturnsConfigUnchanged(): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $this->markTestSkipped('The unchanged-config fallback applies only to PHP < 8.4.');
        }

        $config = ['arguments' => [Subject::class . '\\Proxy' => []]];
        self::assertSame($config, $this->createLazyProxies()->modify($config));
    }

    /**
     * Keep exactly the plain, preferred and intercepted subjects in the allow-list.
     */
    #[RequiresPhp('>= 8.4')]
    public function testOnlyEligibleProxiesAreRecorded(): void
    {
        $config = [
            'arguments' => [
                'Consumer' => [
                    'plain' => ['_i_' => Subject::class . '\\Proxy'],
                    'preferred' => ['_i_' => AnInterface::class . '\\Proxy'],
                    'nested' => ['_vac_' => [['_ins_' => PlainClass::class . '\\Proxy']]],
                    'abstract' => ['_i_' => AnAbstract::class . '\\Proxy'],
                    'internal' => ['_i_' => ExtendsInternal::class . '\\Proxy'],
                    'nonShared' => ['_i_' => AFinalClass::class . '\\Proxy'],
                ],
                Subject::class . '\\Proxy' => [
                    'instanceName' => ['_v_' => Subject::class],
                    'shared' => ['_v_' => true],
                ],
                AFinalClass::class . '\\Proxy' => ['shared' => ['_v_' => false]],
            ],
            'preferences' => [AnInterface::class => Subject::class],
            'instanceTypes' => [PlainClass::class => Interceptor::class],
        ];

        $result = $this->createLazyProxies()->modify($config);

        self::assertSame([
            Subject::class . '\\Proxy' => Subject::class,
            AnInterface::class . '\\Proxy' => Subject::class,
            PlainClass::class . '\\Proxy' => Interceptor::class,
        ], $result['lazyProxies']);
    }

    /**
     * Discover proxy names throughout every supported compiled configuration shape.
     */
    #[RequiresPhp('>= 8.4')]
    #[DataProvider('candidateConfigs')]
    public function testCandidateSources(array $config): void
    {
        self::assertSame(
            [Subject::class . '\\Proxy' => Subject::class],
            $this->createLazyProxies()->modify($config)['lazyProxies']
        );
    }

    /**
     * Supply independent candidate locations, including deeply nested non-shared arguments.
     *
     * @return array
     */
    public static function candidateConfigs(): array
    {
        $proxy = Subject::class . '\\Proxy';
        return [
            'argument key' => [['arguments' => [$proxy => null]]],
            'shared argument' => [['arguments' => ['Consumer' => ['dependency' => ['_i_' => $proxy]]]]],
            'nested non-shared' => [[
                'arguments' => ['Consumer' => ['items' => ['_vac_' => ['nested' => [['_ins_' => $proxy]]]]]],
            ]],
            'instance type value' => [['instanceTypes' => ['VirtualProxy' => $proxy]]],
            'preference key' => [['preferences' => [$proxy => Subject::class]]],
            'preference value' => [['preferences' => ['Dependency' => $proxy]]],
        ];
    }

    /**
     * Reject overrides whose runtime behavior differs from the default generated proxy.
     */
    #[RequiresPhp('>= 8.4')]
    #[DataProvider('proxyOverrides')]
    public function testNonDefaultProxyArgumentsAreExcluded(array $arguments): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [Subject::class . '\\Proxy' => $arguments],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Supply explicit overrides and environment-dependent values that cannot be proven safe.
     *
     * @return array
     */
    public static function proxyOverrides(): array
    {
        return [
            'non-shared' => [['shared' => ['_v_' => false]]],
            'non-boolean shared' => [['shared' => ['_v_' => 1]]],
            'different instance' => [['instanceName' => ['_v_' => PlainClass::class]]],
            'null instance' => [['instanceName' => ['_vn_' => true]]],
            'environment instance' => [['instanceName' => ['_a_' => 'subject', '_d_' => Subject::class]]],
            'environment sharing' => [['shared' => ['_a_' => 'sharing', '_d_' => true]]],
        ];
    }

    /**
     * Exclude every PHP type that cannot supply a native proxy target.
     */
    #[RequiresPhp('>= 8.4')]
    public function testUnsupportedTargetsAndLiteralNamesAreExcluded(): void
    {
        $arguments = [];
        $subjects = [
            AnInterface::class,
            AnAbstract::class,
            AnEnum::class,
            AReadonlyClass::class,
            ATrait::class,
            ExtendsInternal::class,
            \SplQueue::class,
            'Missing\\Subject',
        ];
        foreach ($subjects as $subject) {
            $arguments[$subject . '\\Proxy'] = [];
        }
        $arguments['Consumer'] = ['literal' => ['_v_' => Subject::class . '\\Proxy']];

        self::assertSame([], $this->createLazyProxies()->modify(['arguments' => $arguments])['lazyProxies']);
    }

    /**
     * Keep the generated proxy when the resolved target cannot stand in for the subject.
     */
    #[RequiresPhp('>= 8.4')]
    #[DataProvider('unassignableTargets')]
    public function testTargetsThatCannotReplaceTheGeneratedProxyAreExcluded(string $proxyType, array $config): void
    {
        $config['arguments'] = [$proxyType => []];

        self::assertArrayNotHasKey($proxyType, $this->createLazyProxies()->modify($config)['lazyProxies']);
    }

    /**
     * Supply preferences to an unrelated class and to another proxy.
     *
     * @return array
     */
    public static function unassignableTargets(): array
    {
        return [
            'preference to an unrelated class' => [
                Subject::class . '\\Proxy',
                ['preferences' => [Subject::class => PlainClass::class]],
            ],
            'instance type of an unrelated class' => [
                Subject::class . '\\Proxy',
                ['instanceTypes' => [Subject::class => PlainClass::class]],
            ],
            'preference to another proxy' => [
                AnInterface::class . '\\Proxy',
                ['preferences' => [AnInterface::class => WrittenSubject::class . '\\Proxy']],
            ],
        ];
    }

    /**
     * Resolve instance types after preferences, matching the compiled object manager.
     */
    #[RequiresPhp('>= 8.4')]
    public function testPreferredSubjectUsesItsInterceptor(): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [AnInterface::class . '\\Proxy' => []],
            'preferences' => [AnInterface::class => Subject::class],
            'instanceTypes' => [Subject::class => Interceptor::class],
        ]);

        self::assertSame([AnInterface::class . '\\Proxy' => Interceptor::class], $result['lazyProxies']);
    }

    /**
     * Accept the leading namespace separator used by generated constructor defaults.
     */
    #[RequiresPhp('>= 8.4')]
    public function testGeneratedInstanceNameDefaultIsEligible(): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [Subject::class . '\\Proxy' => [
                'instanceName' => ['_v_' => '\\' . Subject::class],
                'shared' => ['_v_' => true],
            ]],
        ]);

        self::assertSame([Subject::class . '\\Proxy' => Subject::class], $result['lazyProxies']);
    }

    /**
     * Retain generated subclasses needed to satisfy explicitly proxy-typed constructors.
     */
    #[RequiresPhp('>= 8.4')]
    public function testExplicitProxyParameterRequiresGeneratedProxy(): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [ProxyConsumer::class => [
                'dependency' => ['_i_' => Subject::class . '\\Proxy'],
            ]],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Recompute eligibility instead of trusting stale entries from a previous scan.
     */
    #[RequiresPhp('>= 8.4')]
    public function testStaleCompileMapIsDiscarded(): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [Subject::class . '\\Proxy' => ['shared' => ['_v_' => false]]],
            'lazyProxies' => [Subject::class . '\\Proxy' => Subject::class],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Keep hand-written proxy behavior even when its subject would otherwise qualify.
     */
    #[RequiresPhp('>= 8.4')]
    public function testHandWrittenProxyIsExcluded(): void
    {
        $proxy = WrittenSubject\Proxy::class;
        self::assertNotFalse((new \ReflectionClass($proxy))->getFileName());

        $result = $this->createLazyProxies()->modify(['arguments' => [$proxy => []]]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * A virtual type ending in Proxy is not a generated wrapper for the stripped subject.
     */
    #[RequiresPhp('>= 8.4')]
    public function testVirtualTypeNamedProxyIsExcluded(): void
    {
        $proxy = Subject::class . '\\Proxy';
        $result = $this->createLazyProxies()->modify([
            'arguments' => [$proxy => []],
            'instanceTypes' => [$proxy => Subject::class],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Recognize generated proxies under the configured generated-code directory.
     */
    #[RequiresPhp('>= 8.4')]
    public function testGeneratedProxyFileIsEligible(): void
    {
        $proxy = Subject::class . '\\Proxy';
        $directoryList = new DirectoryList(TESTS_TEMP_DIR);
        $reflection = new \ReflectionClass($proxy);
        self::assertStringStartsWith(
            (new File())->getRealPath($directoryList->getPath(DirectoryList::GENERATED_CODE)) . '/',
            $reflection->getFileName()
        );

        $result = (new LazyProxies($directoryList, new File()))->modify(['arguments' => [$proxy => []]]);

        self::assertSame([$proxy => Subject::class], $result['lazyProxies']);
    }

    /**
     * A generated/code substring does not qualify a file outside the configured directory.
     */
    #[RequiresPhp('>= 8.4')]
    public function testProxyOutsideConfiguredDirectoryIsExcluded(): void
    {
        $directoryList = new DirectoryList(TESTS_TEMP_DIR, [
            DirectoryList::GENERATED_CODE => [DirectoryList::PATH => TESTS_TEMP_DIR . '/other/generated/code'],
        ]);
        $result = (new LazyProxies($directoryList, new File()))->modify([
            'arguments' => [Subject::class . '\\Proxy' => []],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Never expose a subject reset hook through native lazy proxy construction.
     */
    #[RequiresPhp('>= 8.4')]
    public function testResettableTargetIsExcluded(): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [AnInterface::class . '\\Proxy' => []],
            'preferences' => [AnInterface::class => ResettableSubject::class],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Keep the generated proxy for subjects marked #[NonLazy] on the class, a parent, an interface or a trait.
     *
     * A native proxy runs methods that touch no instance state without constructing the real instance, which
     * would skip the constructor side effects the attribute exists to preserve.
     */
    #[RequiresPhp('>= 8.4')]
    #[DataProvider('nonLazySubjects')]
    public function testNonLazyTargetIsExcluded(string $subject): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [$subject . '\\Proxy' => []],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Supply stateful subjects that carry #[NonLazy] in each supported position.
     *
     * @return array
     */
    public static function nonLazySubjects(): array
    {
        return [
            'class' => [NonLazySubject::class],
            'parent' => [ExtendsNonLazySubject::class],
            'interface' => [NonLazyInterfaceSubject::class],
            'nested trait' => [NonLazyTraitSubject::class],
        ];
    }

    /**
     * Reject targets for which PHP has no instance state to defer.
     */
    #[RequiresPhp('>= 8.4')]
    public function testTargetsWithoutInstancePropertiesAreExcluded(): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [
                StatelessSubject::class . '\\Proxy' => [],
                StaticOnlySubject::class . '\\Proxy' => [],
            ],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Inspect proxy names inside both nullable and union constructor parameters.
     */
    #[RequiresPhp('>= 8.4')]
    #[DataProvider('compoundProxyConsumers')]
    public function testCompoundProxyParameterRequiresGeneratedProxy(string $consumer): void
    {
        $result = $this->createLazyProxies()->modify([
            'arguments' => [$consumer => ['dependency' => ['_i_' => Subject::class . '\\Proxy']]],
        ]);

        self::assertSame([], $result['lazyProxies']);
    }

    /**
     * Supply constructor signatures that include a generated proxy requirement.
     *
     * @return array
     */
    public static function compoundProxyConsumers(): array
    {
        return [
            'nullable' => [NullableProxyConsumer::class],
            'union with null' => [UnionProxyConsumer::class],
        ];
    }

    /**
     * Match the directory used by the unit bootstrap's generated-class autoloader.
     */
    private function createLazyProxies(): LazyProxies
    {
        return new LazyProxies(new DirectoryList(TESTS_TEMP_DIR), new File());
    }
}
