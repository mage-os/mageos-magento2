<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Registry;

use Magento\Framework\Registry;
use Magento\Framework\Test\Unit\Registry\Fixture\CurrentEntity;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

/**
 * Verifies current-entity storage delegates to the shared Registry.
 */
class AbstractCurrentEntityTest extends TestCase
{
    /**
     * @var Registry
     */
    private Registry $registry;

    /**
     * @var CurrentEntity
     */
    private CurrentEntity $holder;

    /**
     * Create a real Registry and expose the protected operations for testing.
     */
    protected function setUp(): void
    {
        $this->registry = new Registry();
        $this->holder = new CurrentEntity($this->registry);
    }

    /**
     * Return the exact object registered under the holder key.
     */
    public function testFetchReturnsStoredObject(): void
    {
        $entity = new stdClass();
        $this->registry->register('current_entity', $entity);

        self::assertSame($entity, $this->holder->fetchEntity(stdClass::class));
    }

    /**
     * Return null when no value is registered.
     */
    public function testFetchReturnsNullForAbsentKey(): void
    {
        self::assertNull($this->holder->fetchEntity(stdClass::class));
    }

    /**
     * Return null when the registered object has another class.
     */
    public function testFetchReturnsNullForWrongClass(): void
    {
        $this->registry->register('current_entity', new Registry());

        self::assertNull($this->holder->fetchEntity(stdClass::class));
    }

    /**
     * Return null when legacy code registers a scalar value.
     */
    public function testFetchReturnsNullForScalarValue(): void
    {
        $this->registry->register('current_entity', 'foreign value');

        self::assertNull($this->holder->fetchEntity(stdClass::class));
    }

    /**
     * Store the object where legacy Registry readers can access it.
     */
    public function testStoreRegistersValue(): void
    {
        $entity = new stdClass();

        $this->holder->storeEntity($entity);

        self::assertSame($entity, $this->registry->registry('current_entity'));
    }

    /**
     * Preserve Registry rejection of duplicate registration.
     */
    public function testStoreThrowsForOccupiedKey(): void
    {
        $this->holder->storeEntity(new stdClass());

        $this->expectException(RuntimeException::class);
        $this->holder->storeEntity(new stdClass());
    }

    /**
     * Remove the value for both holder and legacy readers.
     */
    public function testClearUnregistersValue(): void
    {
        $this->registry->register('current_entity', new stdClass());

        $this->holder->clear();

        self::assertNull($this->registry->registry('current_entity'));
        self::assertNull($this->holder->fetchEntity(stdClass::class));
    }
}
