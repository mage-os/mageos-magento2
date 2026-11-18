<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Registry\Fixture;

use Magento\Framework\Registry\AbstractCurrentEntity;

/**
 * Concrete holder that exposes the protected Registry operations to tests.
 */
class CurrentEntity extends AbstractCurrentEntity
{
    /**
     * Expose the protected lookup.
     *
     * @param string $class
     * @return object|null
     */
    public function fetchEntity(string $class): ?object
    {
        return $this->fetch($class);
    }

    /**
     * Expose the protected write.
     *
     * @param object $entity
     * @return void
     */
    public function storeEntity(object $entity): void
    {
        $this->store($entity);
    }

    /**
     * @inheritdoc
     */
    protected function getRegistryKey(): string
    {
        return 'current_entity';
    }
}
