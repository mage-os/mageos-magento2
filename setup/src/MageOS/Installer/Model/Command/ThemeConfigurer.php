<?php

declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use MageOS\Installer\Model\VO\DatabaseConfiguration;
use MageOS\Installer\Model\VO\ThemeConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configures and applies Magento theme to store view
 */
class ThemeConfigurer
{
    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Apply selected theme to default store view
     *
     * @param ThemeConfiguration $themeConfig
     * @param DatabaseConfiguration $dbConfig
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function apply(
        ThemeConfiguration $themeConfig,
        DatabaseConfiguration $dbConfig,
        string $baseDir,
        OutputInterface $output
    ): bool {
        if (!$themeConfig->install || empty($themeConfig->theme)) {
            return true; // No theme to apply
        }

        $output->writeln('');
        $output->write('<comment>🎨 Applying theme...</comment>');

        // Get theme ID from theme table
        $themeId = $this->getThemeId($themeConfig->theme, $dbConfig);

        if ($themeId === null) {
            $output->writeln(' <comment>⚠️</comment>');
            $output->writeln("<comment>⚠️  Could not find theme '{$themeConfig->theme}' in registry</comment>");
            $output->writeln(
                '<comment>   You can apply it manually from Admin'
                . ' > Content > Design > Configuration</comment>'
            );
            return false;
        }

        // Apply theme to default store view (store_id = 0 = all stores)
        $result = $this->processRunner->runMagentoCommand(
            ['config:set', 'design/theme/theme_id', (string) $themeId, '--scope=default', '--scope-code=0'],
            $baseDir,
            timeout: 30
        );

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln("<info>✓ Theme '{$themeConfig->theme}' applied successfully!</info>");

            // Clear relevant caches
            $this->processRunner->runMagentoCommand(
                ['cache:clean', 'config', 'layout', 'full_page'],
                $baseDir,
                timeout: 30
            );

            return true;
        }

        $output->writeln(' <comment>⚠️</comment>');
        $output->writeln('<comment>⚠️  Theme application failed. Apply manually from admin panel.</comment>');
        return false;
    }

    /**
     * Get theme ID by querying the database directly
     *
     * @param string $themeCode Theme code (e.g., 'hyva', 'Hyva/default')
     * @param DatabaseConfiguration $dbConfig
     * @return int|null Theme ID or null if not found
     */
    private function getThemeId(string $themeCode, DatabaseConfiguration $dbConfig): ?int
    {
        try {
            $pdo = new \PDO(
                sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', $dbConfig->host, $dbConfig->name),
                $dbConfig->user,
                $dbConfig->password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );

            $stmt = $pdo->prepare(
                "SELECT theme_id FROM theme WHERE area = 'frontend' AND theme_path LIKE :path LIMIT 1"
            );
            $stmt->execute([':path' => '%' . $themeCode . '%']);
            $id = (int) $stmt->fetchColumn();

            return $id > 0 ? $id : null;
        } catch (\PDOException $e) {
            return null;
        }
    }
}
