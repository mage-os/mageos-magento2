<?php
/**
 * Copyright © Mage-OS, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MessageQueue\Model\QueueConfig;

/**
 * Interface for detecting changes between queue configuration and actual state
 */
interface ChangeDetectorInterface
{
    /**
     * Check if there are changes between queue configuration and actual state
     *
     * @return bool
     */
    public function hasChanges(): bool;
}
