<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

/**
 * Collects environment type configuration
 */
class EnvironmentConfig
{
    public const ENV_DEVELOPMENT = 'development';
    public const ENV_PRODUCTION = 'production';

    /**
     * Collect environment type
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return array{type: string, mageMode: string}
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): array {
        $output->writeln('');
        $output->writeln('<info>=== Environment Type ===</info>');

        $choices = [
            'Development (debug mode, sample data recommended)',
            'Production (optimized, no sample data)'
        ];

        $environmentQuestion = new ChoiceQuestion(
            '? Installation environment: ',
            $choices,
            0  // Development is default
        );

        $selected = $questionHelper->ask($input, $output, $environmentQuestion);

        $isDevelopment = $selected === $choices[0];

        return [
            'type' => $isDevelopment ? self::ENV_DEVELOPMENT : self::ENV_PRODUCTION,
            'mageMode' => $isDevelopment ? 'developer' : 'production'
        ];
    }

    /**
     * Get recommended defaults based on environment
     *
     * @param string $environmentType
     * @return array{
     *     debugMode: bool,
     *     sampleData: bool,
     *     logLevel: string
     * }
     */
    public function getRecommendedDefaults(string $environmentType): array
    {
        if ($environmentType === self::ENV_PRODUCTION) {
            return [
                'debugMode' => false,
                'sampleData' => false,
                'logLevel' => 'error'
            ];
        }

        return [
            'debugMode' => true,
            'sampleData' => true,
            'logLevel' => 'debug'
        ];
    }
}
