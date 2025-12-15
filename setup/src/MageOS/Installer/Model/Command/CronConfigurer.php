<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configures Magento cron
 */
class CronConfigurer
{
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Configure cron
     *
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function configure(string $baseDir, OutputInterface $output): bool
    {
        $output->writeln('');
        $output->write('<comment>🔄 Configuring cron...</comment>');

        $result = $this->processRunner->runMagentoCommand('cron:install', $baseDir, timeout: 30);

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ Cron configured successfully!</info>');
            return true;
        }

        // Failed - show manual instructions
        $output->writeln(' <comment>⚠️</comment>');
        $output->writeln('<comment>⚠️  Automatic cron setup failed. Configure manually:</comment>');
        $output->writeln('');
        $output->writeln('<comment>Add to crontab (crontab -e):</comment>');
        $output->writeln(sprintf('<comment>* * * * * %s/bin/magento cron:run 2>&1 | grep -v "Ran jobs"</comment>', $baseDir));
        $output->writeln(sprintf('<comment>* * * * * %s/bin/magento setup:cron:run 2>&1</comment>', $baseDir));
        $output->writeln('');

        if (!empty($result->error)) {
            $output->writeln('<comment>Error: ' . $result->error . '</comment>');
        }

        return false;
    }
}
