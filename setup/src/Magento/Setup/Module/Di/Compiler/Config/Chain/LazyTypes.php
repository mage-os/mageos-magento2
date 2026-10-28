<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Setup\Module\Di\Compiler\Config\Chain;

use Magento\Framework\ObjectManager\Attribute\NonLazy;
use Magento\Setup\Module\Di\Compiler\Config\ModificationInterface;

/**
 * Compile-time scanner that records the allow-list of concrete types provably eligible for
 * PHP 8.4 lazy ghosts. A type qualifies only when it is a loadable, concrete, non-final,
 * non-enum, non-readonly class with no internal PHP ancestor (e.g. ArrayObject, DateTime)
 * and no #[NonLazy] attribute. Types never seen at compile time — such as transitive
 * auto-wired dependencies with no di.xml entry of their own — are simply absent from the
 * allow-list and fall back to eager construction at runtime.
 */
class LazyTypes implements ModificationInterface
{
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
        for ($current = $ref; $current; $current = $current->getParentClass()) {
            if ($current->isInternal()) {
                return false;
            }
        }

        if ($ref->getAttributes(NonLazy::class) !== []) {
            return false;
        }

        return true;
    }
}
