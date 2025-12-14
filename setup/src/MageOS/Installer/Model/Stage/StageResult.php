<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

/**
 * Result returned by each installation stage
 *
 * Indicates what action should be taken next:
 * - CONTINUE: Proceed to next stage
 * - GO_BACK: Navigate to previous stage
 * - RETRY: Re-run current stage
 * - ABORT: Cancel installation
 */
class StageResult
{
    public const CONTINUE = 'continue';
    public const GO_BACK = 'back';
    public const RETRY = 'retry';
    public const ABORT = 'abort';

    /**
     * @param string $status
     * @param string|null $message
     */
    public function __construct(
        public readonly string $status,
        public readonly ?string $message = null
    ) {
        // Validate status
        if (!in_array($status, [self::CONTINUE, self::GO_BACK, self::RETRY, self::ABORT], true)) {
            throw new \InvalidArgumentException(sprintf('Invalid stage result status: %s', $status));
        }
    }

    /**
     * Create a continue result
     *
     * @param string|null $message
     * @return self
     */
    public static function continue(?string $message = null): self
    {
        return new self(self::CONTINUE, $message);
    }

    /**
     * Create a go back result
     *
     * @param string|null $message
     * @return self
     */
    public static function back(?string $message = null): self
    {
        return new self(self::GO_BACK, $message);
    }

    /**
     * Create a retry result
     *
     * @param string|null $message
     * @return self
     */
    public static function retry(?string $message = null): self
    {
        return new self(self::RETRY, $message);
    }

    /**
     * Create an abort result
     *
     * @param string|null $message
     * @return self
     */
    public static function abort(?string $message = null): self
    {
        return new self(self::ABORT, $message);
    }

    /**
     * Check if should continue
     *
     * @return bool
     */
    public function shouldContinue(): bool
    {
        return $this->status === self::CONTINUE;
    }

    /**
     * Check if should go back
     *
     * @return bool
     */
    public function shouldGoBack(): bool
    {
        return $this->status === self::GO_BACK;
    }

    /**
     * Check if should retry
     *
     * @return bool
     */
    public function shouldRetry(): bool
    {
        return $this->status === self::RETRY;
    }

    /**
     * Check if should abort
     *
     * @return bool
     */
    public function shouldAbort(): bool
    {
        return $this->status === self::ABORT;
    }
}
