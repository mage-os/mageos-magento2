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

        // Base URL with retry and auto-correction
        $baseUrl = $this->collectBaseUrl($input, $output, $questionHelper, $baseDir);

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
            'baseUrl' => $baseUrl,
            'language' => $language ?? 'en_US',
            'timezone' => $timezone ?? $defaultTimezone,
            'currency' => $currency ?? 'USD',
            'useRewrites' => (bool)$useRewrites
        ];
    }

    /**
     * Collect and validate base URL with auto-correction
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @param string $baseDir
     * @return string
     */
    private function collectBaseUrl(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper,
        string $baseDir
    ): string {
        $detectedUrl = $this->urlDetector->detect($baseDir);

        while (true) {
            // Ask for URL
            $urlQuestion = new Question(
                sprintf('? Store URL [<comment>%s</comment>]: ', $detectedUrl),
                $detectedUrl
            );
            $enteredUrl = $questionHelper->ask($input, $output, $urlQuestion) ?? $detectedUrl;

            // Normalize the URL
            $normalized = $this->urlValidator->normalize($enteredUrl);

            // If URL was changed, show corrected version
            if ($normalized['changed']) {
                $output->writeln('');
                $output->writeln('<comment>ℹ️  URL has been auto-corrected:</comment>');
                $output->writeln(sprintf('   <comment>Original:</comment>  %s', $enteredUrl));
                $output->writeln(sprintf('   <comment>Corrected:</comment> %s', $normalized['normalized']));

                foreach ($normalized['changes'] as $change) {
                    $output->writeln(sprintf('   <comment>• %s</comment>', $change));
                }

                // Ask if user wants to use corrected version or re-enter
                $acceptQuestion = new ConfirmationQuestion(
                    "\n<question>? Use corrected URL?</question> [<comment>Y/n</comment>]: ",
                    true
                );
                $accept = $questionHelper->ask($input, $output, $acceptQuestion);

                if (!$accept) {
                    $output->writeln('<comment>Please re-enter the URL</comment>');
                    continue;
                }

                $finalUrl = $normalized['normalized'];
            } else {
                $finalUrl = $enteredUrl;
            }

            // Validate the normalized URL
            $validation = $this->urlValidator->validate($finalUrl);

            if (!$validation['valid']) {
                $output->writeln('');
                $output->writeln('<error>❌ ' . $validation['error'] . '</error>');

                $retryQuestion = new ConfirmationQuestion(
                    "\n<question>? Invalid URL. Do you want to try again?</question> [<comment>Y/n</comment>]: ",
                    true
                );
                $retry = $questionHelper->ask($input, $output, $retryQuestion);

                if (!$retry) {
                    throw new \RuntimeException('URL validation failed. Installation aborted.');
                }

                continue;
            }

            // Show HTTPS warning if applicable
            if ($validation['warning']) {
                $output->writeln('<comment>⚠️  ' . $validation['warning'] . '</comment>');
            }

            return $finalUrl;
        }
    }
}
