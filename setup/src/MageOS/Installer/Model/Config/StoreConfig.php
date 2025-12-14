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

        // Most common locales (small curated list)
        $commonLocales = [
            'en_US' => 'English (United States)',
            'en_GB' => 'English (United Kingdom)',
            'de_DE' => 'German (Germany)',
            'fr_FR' => 'French (France)',
            'es_ES' => 'Spanish (Spain)',
            'nl_NL' => 'Dutch (Netherlands)'
        ];

        // Build simple numbered choices
        $choices = [];
        $choiceMap = [];
        $index = 0;

        foreach ($commonLocales as $code => $label) {
            if (isset($locales[$code])) {
                $choices[] = sprintf('%s (%s)', $label, $code);
                $choiceMap[$index] = $code;
                $index++;
            }
        }

        // Add "Other" option
        $choices[] = 'Other (type manually)';
        $otherIndex = $index;

        $output->writeln('');
        $languageQuestion = new ChoiceQuestion(
            '? Default language: ',
            $choices,
            0  // en_US is default
        );

        $selected = $questionHelper->ask($input, $output, $languageQuestion);

        // Check if "Other" was selected
        if ($selected === 'Other (type manually)') {
            $output->writeln('');
            $output->writeln('<comment>ℹ️  Type a locale code (e.g., pt_BR, ja_JP, zh_CN)</comment>');
            $manualQuestion = new Question('? Locale code [<comment>en_US</comment>]: ', 'en_US');
            $manualQuestion->setAutocompleterValues(array_keys($locales));
            $manualCode = $questionHelper->ask($input, $output, $manualQuestion);

            if (isset($locales[$manualCode])) {
                return $manualCode;
            }
            return $manualCode ?? 'en_US';
        }

        // Extract code from selected choice
        foreach ($choiceMap as $idx => $code) {
            if ($choices[$idx] === $selected) {
                return $code;
            }
        }

        return 'en_US';
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

        // Most common timezones (small curated list)
        $commonTimezones = [
            'UTC',
            'America/New_York',
            'America/Chicago',
            'America/Los_Angeles',
            'Europe/London',
            'Europe/Amsterdam',
            'Europe/Berlin'
        ];

        // Build simple numbered choices
        $choices = [];
        $choiceMap = [];
        $index = 0;
        $defaultIndex = 0;

        // Add system timezone first if not in common list
        if (!in_array($systemTimezone, $commonTimezones) && isset($timezones[$systemTimezone])) {
            $choices[] = sprintf('%s (detected)', $timezones[$systemTimezone]);
            $choiceMap[$index] = $systemTimezone;
            $defaultIndex = $index;
            $index++;
        }

        foreach ($commonTimezones as $code) {
            if (isset($timezones[$code])) {
                $label = str_replace(' (' . $code . ')', '', $timezones[$code]);
                $choices[] = sprintf('%s (%s)', $label, $code);
                $choiceMap[$index] = $code;

                if ($code === $systemTimezone) {
                    $defaultIndex = $index;
                }
                $index++;
            }
        }

        // Add "Other" option
        $choices[] = 'Other (type manually)';

        $output->writeln('');
        $timezoneQuestion = new ChoiceQuestion(
            '? Default timezone: ',
            $choices,
            $defaultIndex
        );

        $selected = $questionHelper->ask($input, $output, $timezoneQuestion);

        // Check if "Other" was selected
        if ($selected === 'Other (type manually)') {
            $output->writeln('');
            $output->writeln('<comment>ℹ️  Type a timezone code (e.g., America/Denver, Asia/Tokyo, Europe/Paris)</comment>');
            $manualQuestion = new Question(
                sprintf('? Timezone code [<comment>%s</comment>]: ', $systemTimezone),
                $systemTimezone
            );
            $manualQuestion->setAutocompleterValues(array_keys($timezones));
            $manualCode = $questionHelper->ask($input, $output, $manualQuestion);

            if (isset($timezones[$manualCode])) {
                return $manualCode;
            }
            return $manualCode ?? $systemTimezone;
        }

        // Extract code from selected choice
        foreach ($choiceMap as $idx => $code) {
            if ($choices[$idx] === $selected) {
                return $code;
            }
        }

        return $systemTimezone;
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

        // Most common currencies (small curated list)
        $commonCurrencies = [
            'USD' => 'US Dollar',
            'EUR' => 'Euro',
            'GBP' => 'British Pound',
            'JPY' => 'Japanese Yen',
            'CAD' => 'Canadian Dollar',
            'AUD' => 'Australian Dollar'
        ];

        // Build simple numbered choices
        $choices = [];
        $choiceMap = [];
        $index = 0;

        foreach ($commonCurrencies as $code => $label) {
            if (isset($currencies[$code])) {
                $choices[] = sprintf('%s (%s)', $label, $code);
                $choiceMap[$index] = $code;
                $index++;
            }
        }

        // Add "Other" option
        $choices[] = 'Other (type manually)';

        $output->writeln('');
        $currencyQuestion = new ChoiceQuestion(
            '? Default currency: ',
            $choices,
            0  // USD is default
        );

        $selected = $questionHelper->ask($input, $output, $currencyQuestion);

        // Check if "Other" was selected
        if ($selected === 'Other (type manually)') {
            $output->writeln('');
            $output->writeln('<comment>ℹ️  Type a currency code (e.g., CHF, CNY, INR, SEK)</comment>');
            $manualQuestion = new Question('? Currency code [<comment>USD</comment>]: ', 'USD');
            $manualQuestion->setAutocompleterValues(array_keys($currencies));
            $manualCode = $questionHelper->ask($input, $output, $manualQuestion);

            if (isset($currencies[$manualCode])) {
                return $manualCode;
            }
            return $manualCode ?? 'USD';
        }

        // Extract code from selected choice
        foreach ($choiceMap as $idx => $code) {
            if ($choices[$idx] === $selected) {
                return $code;
            }
        }

        return 'USD';
    }
}
