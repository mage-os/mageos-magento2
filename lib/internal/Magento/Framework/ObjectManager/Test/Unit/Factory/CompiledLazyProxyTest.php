<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\ObjectManager\Test\Unit\Factory;

use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\ObjectManager\Config\Compiled as CompiledConfig;
use Magento\Framework\ObjectManager\ConfigInterface;
use Magento\Framework\ObjectManager\Factory\Compiled;
use Magento\Framework\ObjectManager\LazyProxyAwareInterface;
use Magento\Framework\ObjectManager\ObjectManager;
use Magento\Framework\ObjectManager\Test\Unit\Factory\Fixture\Compiled\NativeProxySubject;
use Magento\Framework\ObjectManager\Test\Unit\Factory\Fixture\Compiled\NativeProxySubject\Proxy;
use Magento\Framework\ObjectManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\TestCase;

/**
 * Verifies native proxies and generated-proxy fallback in compiled DI.
 */
class CompiledLazyProxyTest extends TestCase
{
    /**
     * Resolve the real instance once, only when the proxy first reads its state.
     */
    #[RequiresPhp('>= 8.4')]
    public function testEligibleProxyDefersSubjectResolution(): void
    {
        $real = new NativeProxySubject('real instance result');
        $calls = 0;
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::once())->method('get')->with(NativeProxySubject::class)
            ->willReturnCallback(static function () use ($real, &$calls): NativeProxySubject {
                $calls++;
                return $real;
            });
        $objectManager->expects(self::never())->method('create');
        $factory = new Compiled(new CompiledConfig($this->compiledData()));
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class);

        self::assertInstanceOf(NativeProxySubject::class, $result);
        self::assertNotInstanceOf(Proxy::class, $result);
        $reflection = new \ReflectionClass(NativeProxySubject::class);
        self::assertTrue($reflection->isUninitializedLazyObject($result));
        self::assertSame(0, $calls);
        self::assertSame($real->ping(), $result->ping());
        self::assertFalse($reflection->isUninitializedLazyObject($result));
        self::assertSame($real->ping(), $result->ping());
        self::assertSame(1, $calls);
    }

    /**
     * Preserve generated proxies when any runtime eligibility gate fails.
     */
    #[DataProvider('fallbackCases')]
    public function testGeneratedProxyFallback(bool $eligible, array $globals, array $arguments): void
    {
        $data = $this->compiledData();
        if (!$eligible) {
            unset($data['lazyProxies']);
        }
        $real = new NativeProxySubject();
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::once())->method('get')->with(NativeProxySubject::class)->willReturn($real);
        $shared = [ObjectManagerInterface::class => $objectManager];
        $factory = new Compiled(new CompiledConfig($data), $shared, $globals);
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class, $arguments);

        self::assertInstanceOf(Proxy::class, $result);
        self::assertSame($real->ping(), $result->ping());
    }

    /**
     * Preserve non-shared resolution when a call-time override disables subject sharing.
     */
    public function testCallTimeSharingOverrideUsesCreate(): void
    {
        $real = new NativeProxySubject();
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::never())->method('get');
        $objectManager->expects(self::once())->method('create')->with(NativeProxySubject::class)->willReturn($real);
        $shared = [ObjectManagerInterface::class => $objectManager];
        $factory = new Compiled(new CompiledConfig($this->compiledData()), $shared);
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class, ['shared' => false]);

        self::assertInstanceOf(Proxy::class, $result);
        self::assertSame($real->ping(), $result->ping());
    }

    /**
     * Reflect the concrete real-instance type when preferences or plugins select a subclass.
     */
    #[RequiresPhp('>= 8.4')]
    public function testNativeProxyUsesConcreteTargetClass(): void
    {
        $real = new class ('intercepted result') extends NativeProxySubject {
        };
        $data = $this->compiledData();
        $data['lazyProxies'][Proxy::class] = $real::class;
        $data['preferences'][NativeProxySubject::class] = $real::class;
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::once())->method('get')->with(NativeProxySubject::class)->willReturn($real);
        $factory = new Compiled(new CompiledConfig($data));
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class);

        self::assertSame($real::class, $result::class);
        self::assertTrue((new \ReflectionClass($real))->isUninitializedLazyObject($result));
        self::assertSame($real->ping(), $result->ping());
    }

    /**
     * Keep the generated proxy when a shared instance of another class is already registered for the subject.
     *
     * ObjectManagerFactory seeds App\Filesystem\DirectoryList for Framework\Filesystem\DirectoryList, and
     * addSharedInstance() can do the same; PHP would reject that real instance for a native proxy of the target.
     */
    #[RequiresPhp('>= 8.4')]
    public function testSeededSharedInstanceOfAnotherClassKeepsGeneratedProxy(): void
    {
        $real = new class ('seeded subclass') extends NativeProxySubject {
        };
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::once())->method('get')->with(NativeProxySubject::class)->willReturn($real);
        $shared = [ObjectManagerInterface::class => $objectManager, NativeProxySubject::class => $real];
        $factory = new Compiled(new CompiledConfig($this->compiledData()), $shared);
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class);

        self::assertInstanceOf(Proxy::class, $result);
        self::assertSame('seeded subclass', $result->ping());
    }

    /**
     * Use the native proxy when the registered shared instance is exactly the compiled target.
     */
    #[RequiresPhp('>= 8.4')]
    public function testSeededSharedInstanceOfTargetClassUsesNativeProxy(): void
    {
        $real = new NativeProxySubject('seeded target');
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::once())->method('get')->with(NativeProxySubject::class)->willReturn($real);
        $shared = [ObjectManagerInterface::class => $objectManager, NativeProxySubject::class => $real];
        $factory = new Compiled(new CompiledConfig($this->compiledData()), $shared);
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class);

        self::assertNotInstanceOf(Proxy::class, $result);
        self::assertSame('seeded target', $result->ping());
    }

    /**
     * Keep the generated proxy when a runtime preference no longer resolves to the compiled target.
     */
    #[RequiresPhp('>= 8.4')]
    public function testRuntimePreferenceChangeKeepsGeneratedProxy(): void
    {
        $real = new class ('runtime preference') extends NativeProxySubject {
        };
        $config = new CompiledConfig($this->compiledData());
        $config->extend(['preferences' => [NativeProxySubject::class => $real::class]]);
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::once())->method('get')->with(NativeProxySubject::class)->willReturn($real);
        $shared = [ObjectManagerInterface::class => $objectManager];
        $factory = new Compiled($config, $shared);
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class);

        self::assertInstanceOf(Proxy::class, $result);
        self::assertSame('runtime preference', $result->ping());
    }

    /**
     * Initialize the lazy ghost supplied by the object manager before returning it to PHP.
     */
    #[RequiresPhp('>= 8.4')]
    public function testInitializerAcceptsLazyGhostSubject(): void
    {
        $initializations = 0;
        $reflection = new \ReflectionClass(NativeProxySubject::class);
        $real = $reflection->newLazyGhost(static function (NativeProxySubject $instance) use (&$initializations): void {
            $initializations++;
            $instance->__construct('initialized subject');
        });

        $this->assertLazySubjectIsResolved($real);

        self::assertSame(1, $initializations);
    }

    /**
     * Unwrap a lazy proxy supplied by the object manager before returning its backing instance.
     */
    #[RequiresPhp('>= 8.4')]
    public function testInitializerAcceptsLazyProxySubject(): void
    {
        $initializations = 0;
        $reflection = new \ReflectionClass(NativeProxySubject::class);
        $real = $reflection->newLazyProxy(static function () use (&$initializations): NativeProxySubject {
            $initializations++;
            return new NativeProxySubject('initialized subject');
        });

        $this->assertLazySubjectIsResolved($real);

        self::assertSame(1, $initializations);
    }

    /**
     * Ordinary class requests must not query native proxy configuration.
     */
    public function testNonProxyTypeSkipsLazyProxyLookup(): void
    {
        $config = $this->createMockForIntersectionOfInterfaces([
            ConfigInterface::class,
            LazyProxyAwareInterface::class,
        ]);
        $config->expects(self::never())->method('getLazyProxyTarget');
        $config->method('getInstanceType')->willReturnMap([[NativeProxySubject::class, NativeProxySubject::class]]);
        $config->method('getArguments')->willReturnMap([[NativeProxySubject::class, []]]);

        self::assertInstanceOf(NativeProxySubject::class, (new Compiled($config))->create(NativeProxySubject::class));
    }

    /**
     * Check deferred resolution, initialization and sharing for either kind of lazy real instance.
     */
    private function assertLazySubjectIsResolved(NativeProxySubject $real): void
    {
        $calls = 0;
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::once())->method('get')->with(NativeProxySubject::class)
            ->willReturnCallback(static function () use ($real, &$calls): NativeProxySubject {
                $calls++;
                return $real;
            });
        $factory = new Compiled(new CompiledConfig($this->compiledData()));
        $factory->setObjectManager($objectManager);

        $result = $factory->create(Proxy::class);

        $reflection = new \ReflectionClass(NativeProxySubject::class);
        self::assertTrue($reflection->isUninitializedLazyObject($real));
        self::assertTrue($reflection->isUninitializedLazyObject($result));
        self::assertSame(0, $calls);
        self::assertSame('initialized subject', $result->ping());
        self::assertFalse($reflection->isUninitializedLazyObject($real));
        self::assertFalse($reflection->isUninitializedLazyObject($result));
        $real->setValue('shared subject');
        self::assertSame('shared subject', $result->ping());
        self::assertSame(1, $calls);
    }

    /**
     * Supply each fallback gate independently.
     *
     * @return array
     */
    public static function fallbackCases(): array
    {
        return [
            'no allow-list entry' => [false, [], []],
            'kill switch' => [true, [ConfigOptionsListConstants::CONFIG_PATH_LAZY_OBJECT_LOADING_DISABLED => true], []],
            'call-time arguments' => [true, [], ['shared' => true]],
        ];
    }

    /**
     * Even an eligible compile-time map must retain generated proxies on PHP 8.3.
     */
    public function testPhpBelow84UsesGeneratedProxy(): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $this->markTestSkipped('The PHP version fallback applies only to PHP < 8.4.');
        }

        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::never())->method('get');
        $shared = [ObjectManagerInterface::class => $objectManager];
        $factory = new Compiled(new CompiledConfig($this->compiledData()), $shared);
        $factory->setObjectManager($objectManager);

        self::assertInstanceOf(Proxy::class, $factory->create(Proxy::class));
    }

    /**
     * Configurations that do not advertise native proxy support use existing construction.
     */
    public function testConfigWithoutLazyProxyInterfaceUsesGeneratedProxy(): void
    {
        $config = $this->createMock(ConfigInterface::class);
        $config->method('getInstanceType')->willReturnMap([[Proxy::class, Proxy::class]]);
        $data = $this->compiledData();
        $config->method('getArguments')->willReturnMap([[Proxy::class, $data['arguments'][Proxy::class]]]);
        $objectManager = $this->createMock(ObjectManagerInterface::class);
        $objectManager->expects(self::never())->method('get');
        $shared = [ObjectManagerInterface::class => $objectManager];
        $factory = new Compiled($config, $shared);
        $factory->setObjectManager($objectManager);

        self::assertInstanceOf(Proxy::class, $factory->create(Proxy::class));
    }

    /**
     * Cache the native proxy by the proxy dependency name and share its resolved subject.
     */
    #[RequiresPhp('>= 8.4')]
    public function testObjectManagerCachesProxyAndSharesSubject(): void
    {
        $data = $this->compiledData();
        $data['arguments'][NativeProxySubject::class] = [];
        $config = new CompiledConfig($data);
        $shared = [];
        $factory = new Compiled($config, $shared);
        $objectManager = new ObjectManager($factory, $config, $shared);
        $factory->setObjectManager($objectManager);

        $proxy = $objectManager->get(Proxy::class);

        self::assertSame($proxy, $objectManager->get(Proxy::class));
        self::assertArrayNotHasKey(NativeProxySubject::class, $shared);
        self::assertSame('subject result', $proxy->ping());
        $objectManager->get(NativeProxySubject::class)->setValue('shared instance changed');
        self::assertSame('shared instance changed', $proxy->ping());
    }

    /**
     * Exercise native proxy initialization when the real compiled factory creates the subject as a ghost.
     */
    #[RequiresPhp('>= 8.4')]
    public function testCompiledGhostSubjectIsInitializedThroughNativeProxy(): void
    {
        $data = $this->compiledData();
        $data['arguments'][NativeProxySubject::class] = ['value' => ['_v_' => 'compiled ghost result']];
        $data['lazyTypes'] = [NativeProxySubject::class => true];
        $config = new CompiledConfig($data);
        $shared = [];
        $factory = new Compiled($config, $shared);
        $objectManager = new ObjectManager($factory, $config, $shared);
        $factory->setObjectManager($objectManager);

        $proxy = $objectManager->get(Proxy::class);

        self::assertArrayNotHasKey(NativeProxySubject::class, $shared);
        self::assertSame('compiled ghost result', $proxy->ping());
        $real = $objectManager->get(NativeProxySubject::class);
        self::assertFalse((new \ReflectionClass($real))->isUninitializedLazyObject($real));
        $real->setValue('shared compiled subject');
        self::assertSame('shared compiled subject', $proxy->ping());
    }

    /**
     * Supply default generated-proxy constructor arguments and the eligible native target.
     *
     * @return array
     */
    private function compiledData(): array
    {
        return [
            'arguments' => [Proxy::class => [
                'objectManager' => ['_i_' => ObjectManagerInterface::class],
                'instanceName' => ['_v_' => NativeProxySubject::class],
                'shared' => ['_v_' => true],
            ]],
            'lazyProxies' => [Proxy::class => NativeProxySubject::class],
        ];
    }
}
