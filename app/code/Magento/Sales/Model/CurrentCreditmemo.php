<?php
/**
 * Copyright 2026 Mage-OS
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Sales\Model;

use Magento\Framework\Registry\AbstractCurrentEntity;
use Magento\Sales\Model\Order\Creditmemo;

/**
 * Provides typed access to the current creditmemo in the Registry.
 *
 * @api
 */
class CurrentCreditmemo extends AbstractCurrentEntity
{
    /**
     * Retrieve the current creditmemo.
     *
     * @return Creditmemo|null
     */
    public function get(): ?Creditmemo
    {
        return $this->fetch(Creditmemo::class);
    }

    /**
     * Register the current creditmemo, rejecting an occupied key.
     *
     * @param Creditmemo $entity
     * @return void
     * @throws \RuntimeException
     */
    public function set(Creditmemo $entity): void
    {
        $this->store($entity);
    }

    /**
     * @inheritdoc
     */
    protected function getRegistryKey(): string
    {
        return 'current_creditmemo';
    }
}
