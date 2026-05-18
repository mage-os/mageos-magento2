<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Command;

use MageOS\Installer\Model\VO\EmailConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Configures Magento email settings
 */
class EmailConfigurer
{
    /**
     * @param ProcessRunner $processRunner
     */
    public function __construct(
        private readonly ProcessRunner $processRunner
    ) {
    }

    /**
     * Configure email
     *
     * @param EmailConfiguration $config
     * @param string $baseDir
     * @param OutputInterface $output
     * @return bool True if successful
     */
    public function configure(EmailConfiguration $config, string $baseDir, OutputInterface $output): bool
    {
        $output->writeln('');
        $output->write('<comment>🔄 Configuring email...</comment>');

        if (!$config->isSmtp()) {
            $output->writeln(' <info>✓</info>');
            $output->writeln('<info>✓ Using sendmail for email</info>');
            return true;
        }

        // Configure SMTP
        $commands = [
            ['config:set', 'system/smtp/host', $config->host],
            ['config:set', 'system/smtp/port', (string) $config->port],
        ];

        if ($config->auth && $config->username) {
            $commands[] = ['config:set', 'system/smtp/auth', $config->auth];
            $commands[] = ['config:set', 'system/smtp/username', $config->username];
            if ($config->password) {
                $commands[] = ['config:set', 'system/smtp/password', $config->password];
            }
        }

        // Execute all config:set commands
        foreach ($commands as $command) {
            $result = $this->processRunner->runMagentoCommand($command, $baseDir, timeout: 30);

            if ($result->isFailure()) {
                $output->writeln(' <error>❌</error>');
                $output->writeln('<error>Email configuration failed: ' . $result->error . '</error>');
                $output->writeln(
                    '<comment>Configure manually in Admin > Stores > Configuration'
                    . ' > Advanced > System > Mail Sending Settings</comment>'
                );
                return false;
            }
        }

        $output->writeln(' <info>✓</info>');
        $output->writeln('<info>✓ Email configured successfully!</info>');
        return true;
    }
}
