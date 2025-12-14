<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\StoreConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for StoreConfiguration VO
 */
final class StoreConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): StoreConfiguration
    {
        return new StoreConfiguration(
            baseUrl: 'https://magento.local',
            language: 'en_US',
            currency: 'USD',
            timezone: 'America/Los_Angeles',
            useRewrites: true
        );
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function test_it_constructs_with_all_parameters(): void
    {
        $config = new StoreConfiguration(
            baseUrl: 'https://example.com',
            language: 'en_GB',
            currency: 'GBP',
            timezone: 'Europe/London',
            useRewrites: false
        );

        $this->assertPropertyEquals($config, 'baseUrl', 'https://example.com');
        $this->assertPropertyEquals($config, 'language', 'en_GB');
        $this->assertPropertyEquals($config, 'currency', 'GBP');
        $this->assertPropertyEquals($config, 'timezone', 'Europe/London');
        $this->assertPropertyEquals($config, 'useRewrites', false);
    }

    public function test_to_array_contains_all_fields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('baseUrl', $array);
        $this->assertArrayHasKey('language', $array);
        $this->assertArrayHasKey('currency', $array);
        $this->assertArrayHasKey('timezone', $array);
        $this->assertArrayHasKey('useRewrites', $array);
        $this->assertTrue($array['useRewrites']);
    }

    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'baseUrl' => 'https://shop.test',
            'language' => 'de_DE',
            'currency' => 'EUR',
            'timezone' => 'Europe/Berlin',
            'useRewrites' => false
        ];

        $config = StoreConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'baseUrl', 'https://shop.test');
        $this->assertPropertyEquals($config, 'language', 'de_DE');
        $this->assertPropertyEquals($config, 'currency', 'EUR');
        $this->assertPropertyEquals($config, 'timezone', 'Europe/Berlin');
        $this->assertPropertyEquals($config, 'useRewrites', false);
    }

    public function test_from_array_with_missing_fields_uses_defaults(): void
    {
        $data = ['baseUrl' => 'https://test.local'];

        $config = StoreConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'baseUrl', 'https://test.local');
        $this->assertPropertyEquals($config, 'language', 'en_US');
        $this->assertPropertyEquals($config, 'currency', 'USD');
        $this->assertPropertyEquals($config, 'timezone', 'America/Chicago');
        $this->assertPropertyEquals($config, 'useRewrites', true);
    }

    public function test_handles_various_currencies(): void
    {
        $currencies = ['USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD'];

        foreach ($currencies as $currency) {
            $config = new StoreConfiguration(
                baseUrl: 'https://example.com',
                language: 'en_US',
                currency: $currency,
                timezone: 'UTC',
                useRewrites: true
            );

            $this->assertPropertyEquals($config, 'currency', $currency);
        }
    }

    public function test_handles_various_languages(): void
    {
        $languages = ['en_US', 'en_GB', 'de_DE', 'fr_FR', 'es_ES', 'it_IT'];

        foreach ($languages as $language) {
            $config = new StoreConfiguration(
                baseUrl: 'https://example.com',
                language: $language,
                currency: 'USD',
                timezone: 'UTC',
                useRewrites: true
            );

            $this->assertPropertyEquals($config, 'language', $language);
        }
    }
}
