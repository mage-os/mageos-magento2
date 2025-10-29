<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\SalesRule\Model\Plugin;

class TriggerRecollectState
{
    /**
     * @var int
     */
    private $triggerRecollect= 0;

    /**
     * Set Recollect status
     *
     * @param int $collect
     * @return void
     */
    public function setTriggerRecollect(int $collect): void
    {
        $this->triggerRecollect = $collect;
    }

    /**
     * Check recollect will be triggered
     *
     * @return int
     */
    public function canRecollect(): int
    {
        return $this->triggerRecollect;
    }
}
