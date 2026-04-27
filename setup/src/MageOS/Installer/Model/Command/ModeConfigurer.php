<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configures Magento deployment mode
 */
class ModeConfigurer
{
    /**
     * Constructor
     *
     * @param ProcessRunner $processRunner
     */
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Set Magento deployment mode
     *
     * @param string $mode Mode to set (developer, production, default)
     * @param string $baseDir Magento base directory
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function setMode(string $mode, string $baseDir, OutputInterface $output): bool
    {
        $output->writeln('');
        $output->write(sprintf('<comment>🔄 Setting Magento mode to %s...</comment>', $mode));

        $result = $this->processRunner->runMagentoCommand(
            ['deploy:mode:set', $mode],
            $baseDir,
            timeout: 120 // Mode setting can take time (compilation)
        );

        if ($result->isSuccess()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln(sprintf('<info>✓ Magento mode set to %s</info>', $mode));
            return true;
        }

        // Failed
        $output->writeln(' <comment>⚠️</comment>');
        $output->writeln(sprintf(
            '<comment>⚠️  Mode setting failed. Run manually: bin/magento deploy:mode:set %s</comment>',
            $mode
        ));

        if (!empty($result->error)) {
            $output->writeln('<comment>Error: ' . $result->error . '</comment>');
        }

        return false;
    }
}
