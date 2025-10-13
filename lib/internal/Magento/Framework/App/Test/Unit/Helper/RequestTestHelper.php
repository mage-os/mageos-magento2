<?php
/**
 * Copyright 2025 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Framework\App\Test\Unit\Helper;

use Magento\Framework\Webapi\Request;

/**
 * Test helper for Request with isDispatched method
 *
 * This helper extends Magento\Framework\Webapi\Request which implements RequestInterface
 * and provides access to isDispatched() method for testing purposes.
 *
 * WHY THIS HELPER IS REQUIRED:
 * - Production code (ControllerPostdispatchObserver) calls $request->isDispatched()
 * - Type hint is RequestInterface which does NOT have isDispatched() method
 * - But concrete Request class DOES have isDispatched() (inherited from HTTP Request parent)
 * - Cannot use createPartialMock on interface for non-existent method (PHPUnit 12 limitation)
 *
 * This helper extends Webapi\Request (which implements RequestInterface and extends HTTP Request)
 * and bypasses the constructor to avoid dependency injection issues in tests.
 */
class RequestTestHelper extends Request
{
    public function __construct()
    {
        // Skip parent constructor to avoid dependency injection issues in tests
        // Initialize parent's protected $dispatched property
        $this->dispatched = false;
    }
    
    /**
     * Set dispatched flag
     *
     * @param bool $value
     * @return void
     */
    public function setIsDispatched(bool $value): void
    {
        $this->dispatched = $value;
    }
}
