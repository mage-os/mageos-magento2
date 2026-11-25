<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model;

use Magento\Framework\Registry;
use Magento\Sales\Model\CurrentCreditmemo;
use Magento\Sales\Model\Order\Creditmemo;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Verifies CurrentCreditmemo interoperability with the legacy Registry key.
 */
class CurrentCreditmemoTest extends TestCase
{
    /**
     * Read the exact entity registered by a legacy caller.
     */
    public function testGetReturnsLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $entity = $this->createStub(Creditmemo::class);
        $registry->register('current_creditmemo', $entity);
        $holder = new CurrentCreditmemo($registry);

        self::assertSame($entity, $holder->get());
    }

    /**
     * Expose the entity to legacy Registry readers.
     */
    public function testSetRegistersLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $holder = new CurrentCreditmemo($registry);
        $entity = $this->createStub(Creditmemo::class);

        $holder->set($entity);

        self::assertSame($entity, $registry->registry('current_creditmemo'));
        self::assertSame($entity, $holder->get());
    }

    /**
     * Ignore an entity of an unexpected class.
     */
    public function testGetReturnsNullForWrongClass(): void
    {
        $registry = new Registry();
        $registry->register('current_creditmemo', new stdClass());
        $holder = new CurrentCreditmemo($registry);

        self::assertNull($holder->get());
    }
}
