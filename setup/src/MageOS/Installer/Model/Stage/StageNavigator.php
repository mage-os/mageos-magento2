<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Navigator for installation stages
 *
 * Manages stage execution order, back navigation, and progress tracking.
 */
class StageNavigator
{
    /**
     * @var array<InstallationStageInterface>
     */
    private array $stages;

    /**
     * @var array<int> History of executed stage indices for back navigation
     */
    private array $history = [];

    /**
     * @param array<InstallationStageInterface> $stages
     */
    public function __construct(array $stages)
    {
        $this->stages = $stages;
    }

    /**
     * Execute all stages with navigation support
     *
     * @param InstallationContext $context
     * @param OutputInterface $output
     * @return bool True if completed successfully, false if aborted
     */
    public function navigate(InstallationContext $context, OutputInterface $output): bool
    {
        $currentIndex = 0;
        $totalStages = count($this->stages);

        while ($currentIndex < $totalStages) {
            $stage = $this->stages[$currentIndex];

            // Skip if stage says it should be skipped
            if ($stage->shouldSkip($context)) {
                $currentIndex++;
                continue;
            }

            // Display progress for this stage
            $stepDisplay = $this->getStepDisplay($currentIndex);
            $this->displayStageProgress($output, $stage, $stepDisplay['current'], $stepDisplay['total']);

            // Execute stage
            $result = $stage->execute($context, $output);

            // Handle result
            if ($result->shouldAbort()) {
                return false; // Abort installation
            }

            if ($result->shouldGoBack()) {
                // Go back to previous stage
                if (!empty($this->history)) {
                    $currentIndex = array_pop($this->history);
                }
                continue;
            }

            if ($result->shouldRetry()) {
                // Retry current stage (don't advance or add to history)
                continue;
            }

            // Continue to next stage
            $this->history[] = $currentIndex;
            $currentIndex++;
        }

        return true; // Completed successfully
    }

    /**
     * Display progress for current stage
     *
     * @param OutputInterface $output
     * @param InstallationStageInterface $stage
     * @param int $current
     * @param int $total
     * @return void
     */
    private function displayStageProgress(
        OutputInterface $output,
        InstallationStageInterface $stage,
        int $current,
        int $total
    ): void {
        // Only show progress for stages that have meaningful weight
        if ($stage->getProgressWeight() === 0) {
            return;
        }

        $progress = (int) round(($current / $total) * 100);
        $progressBar = $this->renderProgressBar($progress);

        $output->writeln('');
        $output->writeln("\033[36m═══════════════════════════════════════════════════════\033[0m");
        $output->writeln(sprintf("\033[36m[Step %d/%d] %s\033[0m", $current, $total, $stage->getName()));
        $output->writeln(sprintf("\033[36m%s %d%%\033[0m", $progressBar, $progress));
        $output->writeln("\033[36m═══════════════════════════════════════════════════════\033[0m");
        $output->writeln('');
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
     * Get total progress weight of all stages
     *
     * @return int
     */
    public function getTotalWeight(): int
    {
        $total = 0;
        foreach ($this->stages as $stage) {
            $total += $stage->getProgressWeight();
        }
        return $total;
    }

    /**
     * Get current progress based on completed stages
     *
     * @param int $currentIndex
     * @return int Percentage (0-100)
     */
    public function getProgress(int $currentIndex): int
    {
        $completedWeight = 0;
        $totalWeight = $this->getTotalWeight();

        for ($i = 0; $i < $currentIndex && $i < count($this->stages); $i++) {
            $completedWeight += $this->stages[$i]->getProgressWeight();
        }

        if ($totalWeight === 0) {
            return 0;
        }

        return (int) round(($completedWeight / $totalWeight) * 100);
    }

    /**
     * Get stage count for display (Step X of Y)
     *
     * @param int $currentIndex
     * @return array{current: int, total: int}
     */
    public function getStepDisplay(int $currentIndex): array
    {
        // Filter out skippable stages for cleaner display
        $visibleStages = 0;
        $currentVisible = 0;

        foreach ($this->stages as $index => $stage) {
            // Count this stage as visible
            $visibleStages++;

            if ($index < $currentIndex) {
                $currentVisible++;
            }
        }

        return [
            'current' => $currentVisible + 1,
            'total' => $visibleStages
        ];
    }
}
