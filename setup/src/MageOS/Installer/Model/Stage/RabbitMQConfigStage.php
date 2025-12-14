<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\RabbitMQConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\RabbitMQConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RabbitMQ configuration stage
 */
class RabbitMQConfigStage extends AbstractStage
{
    public function __construct(
        private readonly RabbitMQConfig $rabbitMQConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'RabbitMQ Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure RabbitMQ message queue';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getRabbitMQ() !== null) {
            $rabbitmq = $context->getRabbitMQ();

            if ($rabbitmq->enabled) {
                \Laravel\Prompts\info(sprintf('RabbitMQ: %s:%d', $rabbitmq->host, $rabbitmq->port));
            } else {
                \Laravel\Prompts\info('RabbitMQ: Not configured');
            }

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved RabbitMQ configuration?',
                default: true,
                hint: 'Password will be re-prompted if enabled'
            );

            if ($useExisting) {
                // Re-prompt for password if RabbitMQ is enabled
                if ($rabbitmq->enabled && empty($rabbitmq->password)) {
                    $password = \Laravel\Prompts\password(
                        label: 'RabbitMQ password',
                        hint: 'Password was not saved for security'
                    );

                    $context->setRabbitMQ(new RabbitMQConfiguration(
                        $rabbitmq->enabled,
                        $rabbitmq->host,
                        $rabbitmq->port,
                        $rabbitmq->user,
                        $password,
                        $rabbitmq->virtualHost
                    ));
                }

                return StageResult::continue();
            }
        }

        // Collect RabbitMQ configuration
        $rabbitMQArray = $this->rabbitMQConfig->collect();

        // Store in context
        $context->setRabbitMQ(RabbitMQConfiguration::fromArray($rabbitMQArray));

        return StageResult::continue();
    }
}
