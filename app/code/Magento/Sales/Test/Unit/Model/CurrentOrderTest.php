<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model;

use Magento\Framework\Registry;
use Magento\Sales\Model\CurrentOrder;
use Magento\Sales\Model\Order;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Verifies CurrentOrder interoperability with the legacy Registry key.
 */
class CurrentOrderTest extends TestCase
{
    /**
     * Read the exact entity registered by a legacy caller.
     */
    public function testGetReturnsLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $entity = $this->createStub(Order::class);
        $registry->register('current_order', $entity);
        $holder = new CurrentOrder($registry);

        self::assertSame($entity, $holder->get());
    }

    /**
     * Expose the entity to legacy Registry readers.
     */
    public function testSetRegistersLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $holder = new CurrentOrder($registry);
        $entity = $this->createStub(Order::class);

        $holder->set($entity);

        self::assertSame($entity, $registry->registry('current_order'));
        self::assertSame($entity, $holder->get());
    }

    /**
     * Ignore an entity of an unexpected class.
     */
    public function testGetReturnsNullForWrongClass(): void
    {
        $registry = new Registry();
        $registry->register('current_order', new stdClass());
        $holder = new CurrentOrder($registry);

        self::assertNull($holder->get());
    }
}
