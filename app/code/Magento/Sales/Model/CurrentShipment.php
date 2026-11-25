<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use Magento\Framework\Registry\AbstractCurrentEntity;
use Magento\Sales\Model\Order\Shipment;

/**
 * Provides typed access to the current shipment in the Registry.
 *
 * @api
 */
class CurrentShipment extends AbstractCurrentEntity
{
    /**
     * Retrieve the current shipment.
     *
     * @return Shipment|null
     */
    public function get(): ?Shipment
    {
        return $this->fetch(Shipment::class);
    }

    /**
     * Register the current shipment, rejecting an occupied key.
     *
     * @param Shipment $entity
     * @return void
     * @throws \RuntimeException
     */
    public function set(Shipment $entity): void
    {
        $this->store($entity);
    }

    /**
     * @inheritdoc
     */
    protected function getRegistryKey(): string
    {
        return 'current_shipment';
    }
}
