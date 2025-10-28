<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Customer\Test\Unit\Helper;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\ActionFlag;

/**
 * Test helper for AccountInterface with custom methods
 */
class AccountInterfaceTestHelper implements AccountInterface
{
    /**
     * @var ActionFlag|null
     */
    private $actionFlag = null;

    /**
     * Execute action based on request and return result
     *
     * @return mixed
     */
    public function execute()
    {
        return null;
    }

    /**
     * Get action flag
     *
     * @return ActionFlag|null
     */
    public function getActionFlag(): ?ActionFlag
    {
        return $this->actionFlag;
    }

    /**
     * Set action flag
     *
     * @param ActionFlag $actionFlag
     * @return $this
     */
    public function setActionFlag(ActionFlag $actionFlag): self
    {
        $this->actionFlag = $actionFlag;
        return $this;
    }
}

