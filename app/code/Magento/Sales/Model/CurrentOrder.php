<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use Magento\Framework\Registry\AbstractCurrentEntity;

/**
 * Provides typed access to the current order in the Registry.
 *
 * @api
 */
class CurrentOrder extends AbstractCurrentEntity
{
    /**
     * Retrieve the current order.
     *
     * @return Order|null
     */
    public function get(): ?Order
    {
        return $this->fetch(Order::class);
    }

    /**
     * Register the current order, rejecting an occupied key.
     *
     * @param Order $entity
     * @return void
     * @throws \RuntimeException
     */
    public function set(Order $entity): void
    {
        $this->store($entity);
    }

    /**
     * @inheritdoc
     */
    protected function getRegistryKey(): string
    {
        return 'current_order';
    }
}
