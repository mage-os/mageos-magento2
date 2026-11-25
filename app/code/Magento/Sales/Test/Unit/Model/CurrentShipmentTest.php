<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model;

use Magento\Framework\Registry;
use Magento\Sales\Model\CurrentShipment;
use Magento\Sales\Model\Order\Shipment;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Verifies CurrentShipment interoperability with the legacy Registry key.
 */
class CurrentShipmentTest extends TestCase
{
    /**
     * Read the exact entity registered by a legacy caller.
     */
    public function testGetReturnsLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $entity = $this->createStub(Shipment::class);
        $registry->register('current_shipment', $entity);
        $holder = new CurrentShipment($registry);

        self::assertSame($entity, $holder->get());
    }

    /**
     * Expose the entity to legacy Registry readers.
     */
    public function testSetRegistersLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $holder = new CurrentShipment($registry);
        $entity = $this->createStub(Shipment::class);

        $holder->set($entity);

        self::assertSame($entity, $registry->registry('current_shipment'));
        self::assertSame($entity, $holder->get());
    }

    /**
     * Ignore an entity of an unexpected class.
     */
    public function testGetReturnsNullForWrongClass(): void
    {
        $registry = new Registry();
        $registry->register('current_shipment', new stdClass());
        $holder = new CurrentShipment($registry);

        self::assertNull($holder->get());
    }
}
