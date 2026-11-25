<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Registry;

use Magento\Framework\Registry;

/**
 * Provides current-entity access through a shared Registry key.
 */
abstract class AbstractCurrentEntity
{
    /**
     * Initialize the shared Registry dependency.
     *
     * @param Registry $registry
     */
    public function __construct(private readonly Registry $registry)
    {
    }

    /**
     * Return the Registry key used by this holder.
     *
     * @return string
     */
    abstract protected function getRegistryKey(): string;

    /**
     * Retrieve the current entity only when it matches the expected class.
     *
     * @template T of object
     * @param class-string<T> $class
     * @return T|null
     */
    protected function fetch(string $class): ?object
    {
        $entity = $this->registry->registry($this->getRegistryKey());

        return $entity instanceof $class ? $entity : null;
    }

    /**
     * Register the current entity, rejecting an occupied key.
     *
     * @param object $entity
     * @return void
     * @throws \RuntimeException
     */
    protected function store(object $entity): void
    {
        $this->registry->register($this->getRegistryKey(), $entity);
    }

    /**
     * Remove the current entity from the Registry.
     *
     * @return void
     */
    public function clear(): void
    {
        $this->registry->unregister($this->getRegistryKey());
    }
}
