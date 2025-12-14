<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use MageOS\Installer\Model\Detector\UrlDetector;
use MageOS\Installer\Model\Validator\UrlValidator;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Collects store configuration interactively
 */
class StoreConfig
{
    public function __construct(
        private readonly UrlDetector $urlDetector,
        private readonly UrlValidator $urlValidator
    ) {
    }

    /**
     * Collect store configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param string $baseDir
     * @return array{baseUrl: string, language: string, timezone: string, currency: string, useRewrites: bool}
     */
    public function collect(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        string $baseDir
    ): array {
        $output->writeln('');
        $output->writeln('<info>=== Store Configuration ===</info>');

        // Base URL
        $detectedUrl = $this->urlDetector->detect($baseDir);
        $urlQuestion = new Question(
            sprintf('? Store URL [<comment>%s</comment>]: ', $detectedUrl),
            $detectedUrl
        );
        $urlQuestion->setValidator(function ($answer) {
            $result = $this->urlValidator->validate($answer ?? '');
            if (!$result['valid']) {
                throw new \RuntimeException($result['error'] ?? 'Invalid URL');
            }
            if ($result['warning']) {
                // We'll show the warning but not fail validation
            }
            return $answer;
        });
        $baseUrl = $questionHelper->ask($input, $output, $urlQuestion);

        // Show warning if applicable
        $urlValidation = $this->urlValidator->validate($baseUrl ?? $detectedUrl);
        if ($urlValidation['warning']) {
            $output->writeln('<comment>⚠️  ' . $urlValidation['warning'] . '</comment>');
        }

        // Language
        $languageQuestion = new Question('? Default language [<comment>en_US</comment>]: ', 'en_US');
        $language = $questionHelper->ask($input, $output, $languageQuestion);

        // Timezone
        $defaultTimezone = date_default_timezone_get();
        $timezoneQuestion = new Question(
            sprintf('? Default timezone [<comment>%s</comment>]: ', $defaultTimezone),
            $defaultTimezone
        );
        $timezone = $questionHelper->ask($input, $output, $timezoneQuestion);

        // Currency
        $currencyQuestion = new Question('? Default currency [<comment>USD</comment>]: ', 'USD');
        $currency = $questionHelper->ask($input, $output, $currencyQuestion);

        // URL rewrites
        $rewritesQuestion = new ConfirmationQuestion(
            '? Enable URL rewrites? [<comment>Y/n</comment>]: ',
            true
        );
        $useRewrites = $questionHelper->ask($input, $output, $rewritesQuestion);

        return [
            'baseUrl' => $baseUrl ?? $detectedUrl,
            'language' => $language ?? 'en_US',
            'timezone' => $timezone ?? $defaultTimezone,
            'currency' => $currency ?? 'USD',
            'useRewrites' => (bool)$useRewrites
        ];
    }
}
