<?php

declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use MageOS\Installer\Model\VO\ThemeConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configures and applies Magento theme to store view
 */
class ThemeConfigurer
{
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Apply selected theme to default store view
     *
     * @param ThemeConfiguration $themeConfig
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function apply(ThemeConfiguration $themeConfig, string $baseDir, OutputInterface $output): bool
    {
        if (!$themeConfig->install || empty($themeConfig->theme)) {
            return true; // No theme to apply
        }

        $output->writeln('');
        $output->write('<comment>🎨 Applying theme...</comment>');

        // Get theme ID from theme table
        $themeId = $this->getThemeId($themeConfig->theme, $baseDir);

        if ($themeId === null) {
            $output->writeln(' <comment>⚠️</comment>');
            $output->writeln("<comment>⚠️  Could not find theme '{$themeConfig->theme}' in registry</comment>");
            $output->writeln('<comment>   You can apply it manually from Admin > Content > Design > Configuration</comment>');
            return false;
        }

        // Apply theme to default store view (store_id = 0 = all stores)
        $result = $this->processRunner->runMagentoCommand(
            "config:set design/theme/theme_id {$themeId} --scope=default --scope-code=0",
            $baseDir,
            timeout: 30
        );

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln("<info>✓ Theme '{$themeConfig->theme}' applied successfully!</info>");

            // Clear relevant caches
            $this->processRunner->runMagentoCommand('cache:clean config layout full_page', $baseDir, timeout: 30);

            return true;
        }

        $output->writeln(' <comment>⚠️</comment>');
        $output->writeln('<comment>⚠️  Theme application failed. Apply manually from admin panel.</comment>');
        return false;
    }

    /**
     * Get theme ID from theme code
     *
     * @param string $themeCode Theme code (e.g., 'hyva-default', 'Hyva/default')
     * @param string $baseDir
     * @return int|null Theme ID or null if not found
     */
    private function getThemeId(string $themeCode, string $baseDir): ?int
    {
        // Try to find theme using CLI
        $result = $this->processRunner->runMagentoCommand(
            'theme:list',
            $baseDir,
            timeout: 30
        );

        if (!$result->isSuccess()) {
            return null;
        }

        // Parse output to find theme ID
        // Format: "| <id> | <area> | <theme_path> | ... |"
        $lines = explode("\n", $result->output);

        foreach ($lines as $line) {
            // Match lines with theme data (contains pipe separators)
            if (!str_contains($line, '|')) {
                continue;
            }

            // Split by pipe and trim
            $parts = array_map('trim', explode('|', $line));

            if (count($parts) < 4) {
                continue;
            }

            // Check if this is our theme
            // Match on theme code or path (handles 'hyva-default' or 'Hyva/default')
            $themePath = $parts[3] ?? '';

            if (stripos($themePath, $themeCode) !== false ||
                stripos($themePath, str_replace('-', '/', $themeCode)) !== false
            ) {
                $themeId = (int) $parts[1];
                if ($themeId > 0) {
                    return $themeId;
                }
            }
        }

        return null;
    }
}
