<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;

/**
 * Abstract base stage with common functionality
 */
abstract class AbstractStage implements InstallationStageInterface
{
    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        // By default, most stages allow going back
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        // Default weight for config collection stages
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function shouldSkip(InstallationContext $context): bool
    {
        // By default, don't skip
        return false;
    }

    /**
     * Display stage header with progress
     *
     * @param string $title
     * @param int $current Current step number
     * @param int $total Total steps
     * @return void
     */
    protected function displayHeader(string $title, int $current, int $total): void
    {
        $progress = $this->calculateProgress($current, $total);
        $progressBar = $this->renderProgressBar($progress);

        echo PHP_EOL;
        echo "\033[36m"; // Cyan color
        echo "═══════════════════════════════════════════════════════" . PHP_EOL;
        echo sprintf("[Step %d/%d] %s", $current, $total, $title) . PHP_EOL;
        echo sprintf("%s %d%%", $progressBar, $progress) . PHP_EOL;
        echo "═══════════════════════════════════════════════════════";
        echo "\033[0m"; // Reset color
        echo PHP_EOL;
        echo PHP_EOL;
    }

    /**
     * Calculate progress percentage
     *
     * @param int $current
     * @param int $total
     * @return int
     */
    private function calculateProgress(int $current, int $total): int
    {
        if ($total === 0) {
            return 0;
        }

        return (int) round(($current / $total) * 100);
    }

    /**
     * Render ASCII progress bar
     *
     * @param int $percentage
     * @return string
     */
    private function renderProgressBar(int $percentage): string
    {
        $barLength = 50;
        $filledLength = (int) round(($percentage / 100) * $barLength);
        $emptyLength = $barLength - $filledLength;

        return '[' . str_repeat('█', $filledLength) . str_repeat('▒', $emptyLength) . ']';
    }

    /**
     * Ask if user wants to go back
     *
     * @return bool
     */
    protected function askGoBack(): bool
    {
        if (!$this->canGoBack()) {
            return false;
        }

        return \Laravel\Prompts\confirm(
            label: 'Go back to previous step?',
            default: false,
            hint: 'You can change your previous answers'
        );
    }
}
