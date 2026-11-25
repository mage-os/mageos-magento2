<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model;

use Magento\Framework\Registry;
use Magento\Sales\Model\CurrentInvoice;
use Magento\Sales\Model\Order\Invoice;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Verifies CurrentInvoice interoperability with the legacy Registry key.
 */
class CurrentInvoiceTest extends TestCase
{
    /**
     * Read the exact entity registered by a legacy caller.
     */
    public function testGetReturnsLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $entity = $this->createStub(Invoice::class);
        $registry->register('current_invoice', $entity);
        $holder = new CurrentInvoice($registry);

        self::assertSame($entity, $holder->get());
    }

    /**
     * Expose the entity to legacy Registry readers.
     */
    public function testSetRegistersLegacyRegistryValue(): void
    {
        $registry = new Registry();
        $holder = new CurrentInvoice($registry);
        $entity = $this->createStub(Invoice::class);

        $holder->set($entity);

        self::assertSame($entity, $registry->registry('current_invoice'));
        self::assertSame($entity, $holder->get());
    }

    /**
     * Ignore an entity of an unexpected class.
     */
    public function testGetReturnsNullForWrongClass(): void
    {
        $registry = new Registry();
        $registry->register('current_invoice', new stdClass());
        $holder = new CurrentInvoice($registry);

        self::assertNull($holder->get());
    }
}
