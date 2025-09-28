<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Test\Unit\App\Action\Stub\ActionStub;

class ActionStubTestHelper extends ActionStub
{
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    public function setDirtyRulesNoticeMessage($message)
    {
        return $this;
    }
}
