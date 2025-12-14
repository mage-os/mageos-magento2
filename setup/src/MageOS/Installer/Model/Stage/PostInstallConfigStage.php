<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\CronConfig;
use MageOS\Installer\Model\Config\EmailConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\CronConfiguration;
use MageOS\Installer\Model\VO\EmailConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Post-installation configuration stage (Cron, Email, etc.)
 */
class PostInstallConfigStage extends AbstractStage
{
    public function __construct(
        private readonly CronConfig $cronConfig,
        private readonly EmailConfig $emailConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Post-Installation Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure cron and email';
    }

    /**
     * @inheritDoc
     */
    public function getProgressWeight(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function canGoBack(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $output->writeln('');
        $output->writeln('<fg=cyan>═══════════════════════════════════════════════════════</>');
        $output->writeln('<fg=cyan>Post-Installation Configuration</>');
        $output->writeln('<fg=cyan>═══════════════════════════════════════════════════════</>');

        // Collect cron configuration
        $cronArray = $this->cronConfig->collect();
        $cronConfig = CronConfiguration::fromArray($cronArray);
        $context->setCron($cronConfig);

        if ($cronConfig->configure) {
            $this->configureCron($output);
        }

        // Collect email configuration
        $emailArray = $this->emailConfig->collect();
        $emailConfig = EmailConfiguration::fromArray($emailArray);
        $context->setEmail($emailConfig);

        if ($emailConfig->configure) {
            $this->configureEmail($output, $emailConfig);
        }

        // Set Magento mode based on environment
        $env = $context->getEnvironment();
        if ($env) {
            $this->setMagentoMode($output, $env->mageMode);
        }

        return StageResult::continue();
    }

    /**
     * Configure cron
     *
     * @param OutputInterface $output
     * @return void
     */
    private function configureCron(OutputInterface $output): void
    {
        $output->writeln('');
        $output->write('<comment>🔄 Configuring cron...</comment>');

        try {
            $baseDir = BP;
            $cronCommand = sprintf('cd %s && bin/magento cron:install 2>&1', escapeshellarg($baseDir));
            exec($cronCommand, $cronOutput, $returnCode);

            if ($returnCode === 0) {
                $output->writeln(' <info>✓</info>');
                $output->writeln('<info>✓ Cron configured successfully!</info>');
            } else {
                $output->writeln(' <comment>⚠️</comment>');
                $output->writeln('<comment>⚠️  Automatic cron setup failed. Configure manually:</comment>');
                $output->writeln('');
                $output->writeln('<comment>Add to crontab (crontab -e):</comment>');
                $output->writeln(sprintf('<comment>* * * * * %s/bin/magento cron:run 2>&1 | grep -v "Ran jobs"</comment>', $baseDir));
                $output->writeln(sprintf('<comment>* * * * * %s/bin/magento setup:cron:run 2>&1</comment>', $baseDir));
            }
        } catch (\Exception $e) {
            $output->writeln(' <error>❌</error>');
            $output->writeln('<error>Cron configuration failed: ' . $e->getMessage() . '</error>');
        }
    }

    /**
     * Configure email
     *
     * @param OutputInterface $output
     * @param EmailConfiguration $emailConfig
     * @return void
     */
    private function configureEmail(OutputInterface $output, EmailConfiguration $emailConfig): void
    {
        $output->writeln('');
        $output->write('<comment>🔄 Configuring email...</comment>');

        try {
            if ($emailConfig->isSmtp()) {
                $baseDir = BP;

                // Configure SMTP via Magento config
                $commands = [
                    sprintf('bin/magento config:set system/smtp/host %s', escapeshellarg($emailConfig->host)),
                    sprintf('bin/magento config:set system/smtp/port %d', $emailConfig->port),
                ];

                if ($emailConfig->auth && $emailConfig->username) {
                    $commands[] = sprintf('bin/magento config:set system/smtp/auth %s', escapeshellarg($emailConfig->auth));
                    $commands[] = sprintf('bin/magento config:set system/smtp/username %s', escapeshellarg($emailConfig->username));
                    $commands[] = sprintf('bin/magento config:set system/smtp/password %s', escapeshellarg($emailConfig->password));
                }

                foreach ($commands as $cmd) {
                    exec(sprintf('cd %s && %s 2>&1', escapeshellarg($baseDir), $cmd));
                }

                $output->writeln(' <info>✓</info>');
                $output->writeln('<info>✓ Email configured successfully!</info>');
            } else {
                $output->writeln(' <info>✓</info>');
                $output->writeln('<info>✓ Using sendmail for email</info>');
            }
        } catch (\Exception $e) {
            $output->writeln(' <error>❌</error>');
            $output->writeln('<error>Email configuration failed: ' . $e->getMessage() . '</error>');
        }
    }

    /**
     * Set Magento deployment mode
     *
     * @param OutputInterface $output
     * @param string $mode
     * @return void
     */
    private function setMagentoMode(OutputInterface $output, string $mode): void
    {
        $output->writeln('');
        $output->write(sprintf('<comment>🔄 Setting Magento mode to %s...</comment>', $mode));

        try {
            $baseDir = BP;
            $modeCommand = sprintf('cd %s && bin/magento deploy:mode:set %s 2>&1', escapeshellarg($baseDir), escapeshellarg($mode));
            exec($modeCommand, $modeOutput, $returnCode);

            if ($returnCode === 0) {
                $output->writeln(' <info>✓</info>');
                $output->writeln(sprintf('<info>✓ Magento mode set to %s</info>', $mode));
            } else {
                $output->writeln(' <comment>⚠️</comment>');
                $output->writeln(sprintf('<comment>⚠️  Mode setting failed. Run manually: bin/magento deploy:mode:set %s</comment>', $mode));
            }
        } catch (\Exception $e) {
            $output->writeln(' <error>❌</error>');
            $output->writeln('<error>Mode setting failed: ' . $e->getMessage() . '</error>');
        }
    }
}
