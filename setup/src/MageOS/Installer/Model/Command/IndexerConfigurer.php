<?php

declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configures Magento indexers
 */
class IndexerConfigurer
{
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Set all indexers to schedule mode
     *
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function setScheduleMode(string $baseDir, OutputInterface $output): bool
    {
        $output->writeln('');
        $output->write('<comment>⚙️  Setting indexers to schedule mode...</comment>');

        $result = $this->processRunner->runMagentoCommand(
            'indexer:set-mode schedule',
            $baseDir,
            timeout: 30
        );

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ Indexers set to schedule mode (async via cron)</info>');
            $output->writeln('<comment>   Indexers will update automatically via cron jobs</comment>');
            return true;
        }

        $output->writeln(' <comment>⚠️</comment>');
        $output->writeln('<comment>⚠️  Could not set indexer mode automatically</comment>');
        $output->writeln('<comment>   Set manually: bin/magento indexer:set-mode schedule</comment>');

        if (!empty($result->error)) {
            $output->writeln('<comment>   Error: ' . $result->error . '</comment>');
        }

        return false;
    }

    /**
     * Run all indexers
     *
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function reindexAll(string $baseDir, OutputInterface $output): bool
    {
        $output->writeln('');
        $output->write('<comment>🔄 Running initial reindex...</comment>');

        $result = $this->processRunner->runMagentoCommand(
            'indexer:reindex',
            $baseDir,
            timeout: 300 // Reindexing can take time
        );

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ All indexers completed successfully</info>');
            return true;
        }

        $output->writeln(' <comment>⚠️</comment>');
        $output->writeln('<comment>⚠️  Initial reindex had issues (check output above)</comment>');
        return false;
    }
}
