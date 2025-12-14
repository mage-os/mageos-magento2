<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Stage;

use MageOS\Installer\Model\Config\RedisConfig;
use MageOS\Installer\Model\InstallationContext;
use MageOS\Installer\Model\VO\RedisConfiguration;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Redis configuration stage
 */
class RedisConfigStage extends AbstractStage
{
    public function __construct(
        private readonly RedisConfig $redisConfig
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'Redis Configuration';
    }

    /**
     * @inheritDoc
     */
    public function getDescription(): string
    {
        return 'Configure Redis for sessions, cache, and FPC';
    }

    /**
     * @inheritDoc
     */
    public function execute(InstallationContext $context, OutputInterface $output): StageResult
    {
        // Check if already configured
        if ($context->getRedis() !== null) {
            $redis = $context->getRedis();

            if ($redis->isEnabled()) {
                $features = [];
                if ($redis->session) $features[] = 'Session';
                if ($redis->cache) $features[] = 'Cache';
                if ($redis->fpc) $features[] = 'FPC';

                \Laravel\Prompts\info(sprintf('Redis: %s', implode(', ', $features)));
            } else {
                \Laravel\Prompts\info('Redis: Not configured');
            }

            $useExisting = \Laravel\Prompts\confirm(
                label: 'Use saved Redis configuration?',
                default: true
            );

            if ($useExisting) {
                return StageResult::continue();
            }
        }

        // Collect Redis configuration
        $redisArray = $this->redisConfig->collect();

        // Store in context
        $context->setRedis(RedisConfiguration::fromArray($redisArray));

        return StageResult::continue();
    }
}
