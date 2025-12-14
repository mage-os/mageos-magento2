<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Interface for installation stages
 *
 * Each installation step (database config, admin setup, etc.)
 * implements this interface to provide a consistent execution pattern.
 */
interface InstallationStageInterface
{
    /**
     * Get stage name (for display)
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get stage description (for help text)
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Execute the stage
     *
     * @param InstallationContext $context
     * @param OutputInterface $output
     * @return StageResult
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult;

    /**
     * Can user go back from this stage?
     *
     * Some stages (like actual installation) can't be undone
     *
     * @return bool
     */
    public function canGoBack(): bool;

    /**
     * Get progress weight for this stage
     *
     * Used for calculating overall progress.
     * Typical config stages: 1-2 points
     * Installation/heavy stages: 5-10 points
     *
     * @return int
     */
    public function getProgressWeight(): int;

    /**
     * Should this stage be skipped based on context?
     *
     * For example, Redis config can be skipped if not detected.
     *
     * @param InstallationContext $context
     * @return bool
     */
    public function shouldSkip(InstallationContext $context): bool;
}
