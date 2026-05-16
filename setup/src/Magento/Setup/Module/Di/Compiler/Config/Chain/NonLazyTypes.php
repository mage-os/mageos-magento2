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
 * Compile-time scanner that flags concrete types as non-lazy when they are PHP-incompatible
 * with newLazyGhost: interfaces, abstracts, traits, final/enum/readonly classes, and classes
 * extending internal PHP classes (e.g. ArrayObject, DateTime). Also honors classes that
 * opt out by declaring the #[NonLazy] attribute.
 */
class NonLazyTypes implements ModificationInterface
{
    /**
     * Supplement the DI config to record class types that are NOT eligible for lazy object loading
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

        $nonLazy = [];
        foreach (array_keys($candidates) as $class) {
            if (!$this->isLazyEligible($class)) {
                $nonLazy[$class] = true;
            }
        }

        $existing = (isset($config['nonLazyTypes']) && is_array($config['nonLazyTypes']))
            ? $config['nonLazyTypes']
            : [];
        $config['nonLazyTypes'] = array_replace($existing, $nonLazy);
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
