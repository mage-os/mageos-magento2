<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Framework\Test\Unit\Helper;

use Magento\Framework\DataObject;

/**
 * Test helper for DataObject with custom methods
 */
class DataObjectTestHelper extends DataObject
{
    /**
     * @var int|null
     */
    private $error = null;

    /**
     * @var array|null
     */
    private $messages = null;

    /**
     * Get error (custom method for tests)
     *
     * @return int|null
     */
    public function getError(): ?int
    {
        return $this->error;
    }

    /**
     * Set error
     *
     * @param int|null $error
     * @return $this
     */
    public function setTestError(?int $error): self
    {
        $this->error = $error;
        return $this;
    }

    /**
     * Set messages (custom method for tests)
     *
     * @param array|null $messages
     * @return $this
     */
    public function setMessages(?array $messages): self
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * Get messages
     *
     * @return array|null
     */
    public function getMessages(): ?array
    {
        return $this->messages;
    }
}

