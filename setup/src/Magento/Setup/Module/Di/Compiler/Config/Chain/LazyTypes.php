<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Module\Di\Compiler\Config\Chain;

use Magento\Framework\ObjectManager\Attribute\NonLazy;
use Magento\Setup\Module\Di\Compiler\Config\ModificationInterface;
use Magento\Setup\Module\Di\Compiler\ConstructorSideEffectScanner;

/**
 * Compile-time scanner that records the allow-list of concrete types provably eligible for
 * PHP 8.4 lazy ghosts. A type qualifies only when it is a loadable, concrete, non-final,
 * non-enum, non-readonly class with no internal PHP ancestor (e.g. ArrayObject, DateTime)
 * and no #[NonLazy] attribute on itself, an ancestor, an implemented interface, or a used trait.
 * Classes whose constructor is declared in Magento\ or MageOS\ (or that have none) are then lazy
 * by default, unless that constructor delegates upward into one that is not. Others (third-party
 * constructors, and trusted constructors chaining into them) are lazy only when
 * ConstructorSideEffectScanner proves the constructor is side-effect-free, because a ghost's
 * constructor may never run.
 * Types never seen at compile time — such as transitive auto-wired dependencies with no di.xml
 * entry of their own — are simply absent from the allow-list and fall back to eager construction
 * at runtime.
 *
 * A lazy class builds its constructor dependencies only when it is first initialized, so a
 * dependency with side effects (even a #[NonLazy] one, or one created by the ObjectManager
 * fallback) is built at that point rather than at injection. A class whose own methods never touch
 * its state but rely on a dependency's side effects should itself be marked #[NonLazy].
 */
class LazyTypes implements ModificationInterface
{
    /**
     * @param ConstructorSideEffectScanner $constructorScanner
     */
    public function __construct(
        private readonly ConstructorSideEffectScanner $constructorScanner
    ) {
    }

    /**
     * Supplement the DI config to record class types that ARE eligible for lazy object loading
     *
     * @param array $config
     * @return array
     */
    public function modify(array $config): array
    {
        if (\PHP_VERSION_ID < 80400) {
            return $config;
        }

        $candidates = [];
        foreach (array_keys($config['arguments'] ?? []) as $type) {
            $candidates[(string)$type] = true;
        }
        foreach ($config['instanceTypes'] ?? [] as $concrete) {
            if (is_string($concrete) && $concrete !== '') {
                $candidates[$concrete] = true;
            }
        }
        foreach ($config['preferences'] ?? [] as $impl) {
            if (is_string($impl) && $impl !== '') {
                $candidates[$impl] = true;
            }
        }

        $lazy = [];
        foreach (array_keys($candidates) as $class) {
            if ($this->isLazyEligible($class)) {
                $lazy[$class] = true;
            }
        }

        $existing = (isset($config['lazyTypes']) && is_array($config['lazyTypes']))
            ? $config['lazyTypes']
            : [];
        $config['lazyTypes'] = array_replace($existing, $lazy);
        return $config;
    }

    /**
     * Determine whether the given class is eligible for lazy object loading
     *
     * @param string $class
     * @return bool
     */
    private function isLazyEligible(string $class): bool
    {
        if (str_ends_with($class, '\\Proxy')) {
            return false;
        }

        try {
            if (!class_exists($class)) {
                return false;
            }
            $ref = new \ReflectionClass($class);
        } catch (\Throwable) {
            return false;
        }

        // PHP-level disqualifiers
        if ($ref->isInterface() || $ref->isAbstract() || $ref->isTrait()) {
            return false;
        }
        if ($ref->isFinal() || $ref->isEnum() || $ref->isReadOnly()) {
            return false;
        }
        if ($this->isExcluded($ref)) {
            return false;
        }

        $owner = $ref->getConstructor()?->getDeclaringClass()->getName() ?? $ref->getName();
        if ($this->constructorScanner->isTrusted($ref->getName())
            && $this->constructorScanner->isTrusted($owner)
            && !$this->constructorScanner->chainsToUntrustedConstructor($ref)
        ) {
            return true;
        }

        return $this->constructorScanner->isSideEffectFree($ref);
    }

    /**
     * Whether the class has an internal ancestor or #[NonLazy] on itself, an ancestor, an interface or a trait
     *
     * @param \ReflectionClass $ref
     * @return bool
     */
    private function isExcluded(\ReflectionClass $ref): bool
    {
        foreach ($ref->getInterfaces() as $interface) {
            if ($interface->getAttributes(NonLazy::class) !== []) {
                return true;
            }
        }
        for ($current = $ref; $current; $current = $current->getParentClass()) {
            if ($current->isInternal()
                || $current->getAttributes(NonLazy::class) !== []
                || $this->usesNonLazyTrait($current)
            ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Whether a class or trait uses a #[NonLazy] trait, directly or through other traits
     *
     * @param \ReflectionClass $class
     * @return bool
     */
    private function usesNonLazyTrait(\ReflectionClass $class): bool
    {
        foreach ($class->getTraits() as $trait) {
            if ($trait->getAttributes(NonLazy::class) !== [] || $this->usesNonLazyTrait($trait)) {
                return true;
            }
        }
        return false;
    }
}
