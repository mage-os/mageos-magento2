<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

/**
 * Result of process execution
 */
class ProcessResult
{
    /**
     * @param bool $success
     * @param string $output
     * @param string $error
     */
    public function __construct(
        public readonly bool $success,
        public readonly string $output,
        public readonly string $error = ''
    ) {
    }

    /**
     * Check if process succeeded
     *
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Check if process failed
     *
     * @return bool
     */
    public function isFailure(): bool
    {
        return !$this->success;
    }

    /**
     * Get combined output (stdout + stderr)
     *
     * @return string
     */
    public function getCombinedOutput(): string
    {
        $combined = $this->output;
        if (!empty($this->error)) {
            $combined .= PHP_EOL . $this->error;
        }
        return $combined;
    }
}
