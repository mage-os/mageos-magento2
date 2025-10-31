<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

/**
 * Test helper for callable with __invoke method
 */
class CallableTestHelper
{
    /**
     * @var mixed
     */
    private $returnValue = null;

    /**
     * @var int
     */
    private $callCount = 0;

    /**
     * @var int|null
     */
    private $expectedCallCount = null;

    /**
     * Invoke method
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->callCount++;
        return $this->returnValue;
    }

    /**
     * Set return value
     *
     * @param mixed $value
     * @return $this
     */
    public function setReturnValue($value): self
    {
        $this->returnValue = $value;
        return $this;
    }

    /**
     * Set expected call count
     *
     * @param int $count
     * @return $this
     */
    public function setExpectedCallCount(int $count): self
    {
        $this->expectedCallCount = $count;
        return $this;
    }

    /**
     * Get call count
     *
     * @return int
     */
    public function getCallCount(): int
    {
        return $this->callCount;
    }

    /**
     * Verify call count matches expected
     *
     * @return bool
     */
    public function verifyCallCount(): bool
    {
        if ($this->expectedCallCount === null) {
            return true;
        }
        return $this->callCount === $this->expectedCallCount;
    }
}
