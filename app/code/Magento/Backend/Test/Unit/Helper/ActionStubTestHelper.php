<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Backend\Test\Unit\Helper;

use Magento\Backend\Test\Unit\App\Action\Stub\ActionStub;

/**
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class ActionStubTestHelper extends ActionStub
{
    public function __construct()
    {
        // Skip parent constructor for testing
    }
    
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setDirtyRulesNoticeMessage($message)
    {
        return $this;
    }
}
