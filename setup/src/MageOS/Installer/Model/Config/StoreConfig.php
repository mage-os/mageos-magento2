<?php
/**
 * Copyright © Mage-OS. All rights reserved.
 */
declare(strict_types=1);

namespace MageOS\Installer\Model\Config;

use Magento\Framework\Setup\Lists;
use MageOS\Installer\Model\Detector\UrlDetector;
use MageOS\Installer\Model\Validator\UrlValidator;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;
use function Laravel\Prompts\search;
use function Laravel\Prompts\text;
use function Laravel\Prompts\warning;

/**
 * Collects store configuration interactively with Laravel Prompts
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
     * @param string $baseDir
     * @return array{baseUrl: string, language: string, timezone: string, currency: string, useRewrites: bool}
     */
    public function collect(string $baseDir): array
    {
        note('Store Configuration');

        // Base URL with retry and auto-correction
        $baseUrl = $this->collectBaseUrl($baseDir);

        // Language with SEARCH!
        $language = $this->collectLanguage();

        // Timezone with SEARCH!
        $timezone = $this->collectTimezone();

        // Currency with search
        $currency = $this->collectCurrency();

        // URL rewrites
        $useRewrites = confirm(
            label: 'Enable URL rewrites?',
            default: true,
            hint: 'Recommended for clean URLs (requires mod_rewrite or nginx config)'
        );

        return [
            'baseUrl' => $baseUrl,
            'language' => $language,
            'timezone' => $timezone,
            'currency' => $currency,
            'useRewrites' => $useRewrites
        ];
    }

    /**
     * Collect and validate base URL with auto-correction
     *
     * @param string $baseDir
     * @return string
     */
    private function collectBaseUrl(string $baseDir): string
    {
        $detectedUrl = $this->urlDetector->detect($baseDir);

        while (true) {
            $enteredUrl = text(
                label: 'Store URL',
                default: $detectedUrl,
                placeholder: 'http://magento.test/',
                hint: 'Base URL for your storefront'
            );

            // Normalize the URL
            $normalized = $this->urlValidator->normalize($enteredUrl);

            // If URL was changed, show corrected version
            if ($normalized['changed']) {
                warning('URL has been auto-corrected:');
                info('Original:  ' . $enteredUrl);
                info('Corrected: ' . $normalized['normalized']);
                foreach ($normalized['changes'] as $change) {
                    info('• ' . $change);
                }

                $accept = confirm(
                    label: 'Use corrected URL?',
                    default: true
                );

                if (!$accept) {
                    info('Please re-enter the URL');
                    continue;
                }

                $finalUrl = $normalized['normalized'];
            } else {
                $finalUrl = $enteredUrl;
            }

            // Validate the normalized URL
            $validation = $this->urlValidator->validate($finalUrl);

            if (!$validation['valid']) {
                warning($validation['error'] ?? 'Invalid URL');

                $retry = confirm(
                    label: 'Invalid URL. Do you want to try again?',
                    default: true
                );

                if (!$retry) {
                    throw new \RuntimeException('URL validation failed. Installation aborted.');
                }

                continue;
            }

            // Show HTTPS warning if applicable
            if ($validation['warning']) {
                warning($validation['warning']);
            }

            return $finalUrl;
        }
    }

    /**
     * Collect language with search functionality
     *
     * @return string
     */
    private function collectLanguage(): string
    {
        $locales = $this->lists->getLocaleList();

        $language = search(
            label: 'Default language',
            options: function (string $value) use ($locales) {
                if (strlen($value) === 0) {
                    // Show common locales when no search
                    return [
                        'en_US' => 'English (United States)',
                        'en_GB' => 'English (United Kingdom)',
                        'de_DE' => 'German (Germany)',
                        'fr_FR' => 'French (France)',
                        'es_ES' => 'Spanish (Spain)',
                        'nl_NL' => 'Dutch (Netherlands)',
                        'pt_BR' => 'Portuguese (Brazil)',
                        'ja_JP' => 'Japanese (Japan)',
                        'zh_CN' => 'Chinese (China)',
                        'it_IT' => 'Italian (Italy)'
                    ];
                }

                // Filter all locales by search term
                $filtered = [];
                foreach ($locales as $code => $label) {
                    if (str_contains(strtolower($label), strtolower($value)) ||
                        str_contains(strtolower($code), strtolower($value))) {
                        $filtered[$code] = $label;
                        if (count($filtered) >= 20) {
                            break; // Limit results
                        }
                    }
                }
                return $filtered;
            },
            placeholder: 'Type to search (e.g., "english", "german", "ja_JP")...',
            scroll: 10,
            hint: 'Search by language name or locale code'
        );

        return $language;
    }

    /**
     * Collect timezone with search functionality
     *
     * @return string
     */
    private function collectTimezone(): string
    {
        $timezones = $this->lists->getTimezoneList();
        $systemTimezone = date_default_timezone_get();

        $timezone = search(
            label: 'Default timezone',
            options: function (string $value) use ($timezones, $systemTimezone) {
                if (strlen($value) === 0) {
                    // Show common + detected timezone when no search
                    $common = [
                        $systemTimezone => $timezones[$systemTimezone] . ' (detected)',
                        'UTC' => 'Coordinated Universal Time (UTC)',
                        'America/New_York' => 'Eastern Standard Time (America/New_York)',
                        'America/Chicago' => 'Central Standard Time (America/Chicago)',
                        'America/Los_Angeles' => 'Pacific Standard Time (America/Los_Angeles)',
                        'Europe/London' => 'Greenwich Mean Time (Europe/London)',
                        'Europe/Amsterdam' => 'Central European Standard Time (Europe/Amsterdam)',
                        'Europe/Berlin' => 'Central European Standard Time (Europe/Berlin)',
                        'Asia/Tokyo' => 'Japan Standard Time (Asia/Tokyo)',
                        'Australia/Sydney' => 'Australian Eastern Standard Time (Australia/Sydney)'
                    ];

                    // Remove duplicate if system timezone is already in common list
                    if ($systemTimezone !== 'UTC' && isset($common[$systemTimezone])) {
                        unset($common['UTC']);
                        $common = [$systemTimezone => $timezones[$systemTimezone] . ' (detected)'] +
                                  array_slice($common, 1, 9, true);
                    }

                    return $common;
                }

                // Filter all timezones by search term
                $filtered = [];
                foreach ($timezones as $code => $label) {
                    if (str_contains(strtolower($label), strtolower($value)) ||
                        str_contains(strtolower($code), strtolower($value))) {
                        $filtered[$code] = $label;
                        if (count($filtered) >= 20) {
                            break; // Limit results
                        }
                    }
                }
                return $filtered;
            },
            placeholder: 'Type to search (e.g., "tokyo", "new york", "UTC")...',
            scroll: 10,
            hint: 'Search by city, region, or timezone code'
        );

        return $timezone;
    }

    /**
     * Collect currency with search functionality
     *
     * @return string
     */
    private function collectCurrency(): string
    {
        $currencies = $this->lists->getCurrencyList();

        $currency = search(
            label: 'Default currency',
            options: function (string $value) use ($currencies) {
                if (strlen($value) === 0) {
                    // Show common currencies when no search
                    return [
                        'USD' => 'US Dollar (USD)',
                        'EUR' => 'Euro (EUR)',
                        'GBP' => 'British Pound Sterling (GBP)',
                        'JPY' => 'Japanese Yen (JPY)',
                        'CAD' => 'Canadian Dollar (CAD)',
                        'AUD' => 'Australian Dollar (AUD)',
                        'CHF' => 'Swiss Franc (CHF)',
                        'CNY' => 'Chinese Yuan (CNY)'
                    ];
                }

                // Filter all currencies by search term
                $filtered = [];
                foreach ($currencies as $code => $label) {
                    if (str_contains(strtolower($label), strtolower($value)) ||
                        str_contains(strtolower($code), strtolower($value))) {
                        $filtered[$code] = $label;
                        if (count($filtered) >= 20) {
                            break; // Limit results
                        }
                    }
                }
                return $filtered;
            },
            placeholder: 'Type to search (e.g., "dollar", "euro", "USD")...',
            scroll: 10,
            hint: 'Search by currency name or code'
        );

        return $currency;
    }
}
