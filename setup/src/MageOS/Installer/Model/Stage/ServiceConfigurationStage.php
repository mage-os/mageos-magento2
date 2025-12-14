<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\Writer\EnvConfigWriter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Service configuration stage - configures Redis and RabbitMQ after Magento install
 */
class ServiceConfigurationStage extends AbstractStage
{
    public function __construct(
        private readonly EnvConfigWriter $envConfigWriter
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Service Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure Redis and RabbitMQ in env.php';
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
    public function shouldSkip(InstallationContext $context): bool
    {
        $redis = $context->getRedis();
        $rabbitmq = $context->getRabbitMQ();

        // Skip if neither Redis nor RabbitMQ are configured
        return (!$redis || !$redis->isEnabled()) && (!$rabbitmq || !$rabbitmq->enabled);
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        $redis = $context->getRedis();
        $rabbitmq = $context->getRabbitMQ();

        // Configure Redis
        if ($redis && $redis->isEnabled()) {
            $output->writeln('');
            $output->writeln('<comment>🔄 Configuring Redis...</comment>');
            try {
                $this->envConfigWriter->writeRedisConfig($redis->toArray());
                $output->writeln('<info>✓ Redis configured</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>❌ Redis configuration failed: ' . $e->getMessage() . '</error>');
            }
        }

        // Configure RabbitMQ
        if ($rabbitmq && $rabbitmq->enabled) {
            $output->writeln('');
            $output->writeln('<comment>🔄 Configuring RabbitMQ...</comment>');
            try {
                $this->envConfigWriter->writeRabbitMQConfig($rabbitmq->toArray(true)); // Include password
                $output->writeln('<info>✓ RabbitMQ configured</info>');
            } catch (\Exception $e) {
                $output->writeln('<error>❌ RabbitMQ configuration failed: ' . $e->getMessage() . '</error>');
            }
        }

        return StageResult::continue();
    }
}
