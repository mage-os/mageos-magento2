<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use Magento\Framework\Registry\AbstractCurrentEntity;
use Magento\Sales\Model\Order\Invoice;

/**
 * Provides typed access to the current invoice in the Registry.
 *
 * @api
 */
class CurrentInvoice extends AbstractCurrentEntity
{
    /**
     * Retrieve the current invoice.
     *
     * @return Invoice|null
     */
    public function get(): ?Invoice
    {
        return $this->fetch(Invoice::class);
    }

    /**
     * Register the current invoice, rejecting an occupied key.
     *
     * @param Invoice $entity
     * @return void
     * @throws \RuntimeException
     */
    public function set(Invoice $entity): void
    {
        $this->store($entity);
    }

    /**
     * @inheritdoc
     */
    protected function getRegistryKey(): string
    {
        return 'current_invoice';
    }
}
