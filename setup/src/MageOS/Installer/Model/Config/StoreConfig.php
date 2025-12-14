<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use Magento\Framework\Setup\Lists;
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
        private readonly UrlValidator $urlValidator,
        private readonly Lists $lists
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
        $language = $this->collectLanguage($input, $output, $questionHelper);

        // Timezone
        $timezone = $this->collectTimezone($input, $output, $questionHelper);

        // Currency
        $currency = $this->collectCurrency($input, $output, $questionHelper);

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

    /**
     * Collect language configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return string
     */
    private function collectLanguage(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): string {
        $locales = $this->lists->getLocaleList();

        // Most common locales
        $commonLocales = [
            'en_US' => 'English (United States)',
            'en_GB' => 'English (United Kingdom)',
            'de_DE' => 'German (Germany)',
            'fr_FR' => 'French (France)',
            'es_ES' => 'Spanish (Spain)',
            'it_IT' => 'Italian (Italy)',
            'nl_NL' => 'Dutch (Netherlands)',
            'pt_BR' => 'Portuguese (Brazil)',
            'ja_JP' => 'Japanese (Japan)',
            'zh_CN' => 'Chinese (China)',
        ];

        // Build choices array (code => label)
        $choices = [];
        foreach ($commonLocales as $code => $label) {
            if (isset($locales[$code])) {
                $choices[$code] = sprintf('%s (%s)', $label, $code);
            }
        }

        $output->writeln('');
        $output->writeln('<comment>ℹ️  Showing common languages. Type a locale code (e.g., en_US) to use a different one.</comment>');

        $languageQuestion = new ChoiceQuestion(
            '? Default language [<comment>en_US</comment>]: ',
            $choices,
            'en_US'
        );
        $languageQuestion->setAutocompleterValues(array_keys($locales));

        $selected = $questionHelper->ask($input, $output, $languageQuestion);

        // If user typed a code directly, use it; otherwise extract from choice
        foreach ($choices as $code => $label) {
            if ($label === $selected) {
                return $code;
            }
        }

        // User typed a code directly
        if (isset($locales[$selected])) {
            return $selected;
        }

        return $selected ?? 'en_US';
    }

    /**
     * Collect timezone configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return string
     */
    private function collectTimezone(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): string {
        $timezones = $this->lists->getTimezoneList();
        $systemTimezone = date_default_timezone_get();

        // Most common timezones
        $commonTimezones = [
            'America/New_York',
            'America/Chicago',
            'America/Denver',
            'America/Los_Angeles',
            'America/Phoenix',
            'Europe/London',
            'Europe/Paris',
            'Europe/Berlin',
            'Asia/Tokyo',
            'Asia/Shanghai',
            'Australia/Sydney',
            'UTC',
        ];

        // Build choices array
        $choices = [];
        foreach ($commonTimezones as $code) {
            if (isset($timezones[$code])) {
                $choices[$code] = $timezones[$code];
            }
        }

        // Make sure system timezone is in the list
        if (!isset($choices[$systemTimezone]) && isset($timezones[$systemTimezone])) {
            $choices = [$systemTimezone => $timezones[$systemTimezone]] + $choices;
        }

        $output->writeln('');
        $output->writeln('<comment>ℹ️  Showing common timezones. Type a timezone code (e.g., America/New_York) to use a different one.</comment>');

        $timezoneQuestion = new ChoiceQuestion(
            sprintf('? Default timezone [<comment>%s</comment>]: ', $systemTimezone),
            $choices,
            $systemTimezone
        );
        $timezoneQuestion->setAutocompleterValues(array_keys($timezones));

        $selected = $questionHelper->ask($input, $output, $timezoneQuestion);

        // If user typed a code directly, use it; otherwise extract from choice
        foreach ($choices as $code => $label) {
            if ($label === $selected) {
                return $code;
            }
        }

        // User typed a code directly
        if (isset($timezones[$selected])) {
            return $selected;
        }

        return $selected ?? $systemTimezone;
    }

    /**
     * Collect currency configuration
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param QuestionHelper $questionHelper
     * @return string
     */
    private function collectCurrency(
        InputInterface $input,
        OutputInterface $output,
        QuestionHelper $questionHelper
    ): string {
        $currencies = $this->lists->getCurrencyList();

        // Most common currencies
        $commonCurrencies = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar',
            'CHF' => 'Swiss Franc',
            'CNY' => 'Chinese Yuan',
            'INR' => 'Indian Rupee',
        ];

        // Build choices array
        $choices = [];
        foreach ($commonCurrencies as $code => $label) {
            if (isset($currencies[$code])) {
                $choices[$code] = $currencies[$code];
            }
        }

        $output->writeln('');
        $output->writeln('<comment>ℹ️  Showing common currencies. Type a currency code (e.g., USD) to use a different one.</comment>');

        $currencyQuestion = new ChoiceQuestion(
            '? Default currency [<comment>USD</comment>]: ',
            $choices,
            'USD'
        );
        $currencyQuestion->setAutocompleterValues(array_keys($currencies));

        $selected = $questionHelper->ask($input, $output, $currencyQuestion);

        // If user typed a code directly, use it; otherwise extract from choice
        foreach ($choices as $code => $label) {
            if ($label === $selected) {
                return $code;
            }
        }

        // User typed a code directly
        if (isset($currencies[$selected])) {
            return $selected;
        }

        return $selected ?? 'USD';
    }
}
