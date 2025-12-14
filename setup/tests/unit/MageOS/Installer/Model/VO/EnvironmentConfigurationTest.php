<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\EnvironmentConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for EnvironmentConfiguration VO
 */
final class EnvironmentConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): EnvironmentConfiguration
    {
        return new EnvironmentConfiguration(
            type: 'development',
            mageMode: 'developer'
        );
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function test_it_constructs_with_all_parameters(): void
    {
        $config = new EnvironmentConfiguration(
            type: 'production',
            mageMode: 'production'
        );

        $this->assertPropertyEquals($config, 'type', 'production');
        $this->assertPropertyEquals($config, 'mageMode', 'production');
    }

    public function test_is_development_returns_true_for_development_type(): void
    {
        $config = new EnvironmentConfiguration(
            type: 'development',
            mageMode: 'developer'
        );

        $this->assertTrue($config->isDevelopment());
        $this->assertFalse($config->isProduction());
    }

    public function test_is_production_returns_true_for_production_type(): void
    {
        $config = new EnvironmentConfiguration(
            type: 'production',
            mageMode: 'production'
        );

        $this->assertFalse($config->isDevelopment());
        $this->assertTrue($config->isProduction());
    }

    public function test_to_array_contains_all_fields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('mageMode', $array);
        $this->assertEquals('development', $array['type']);
        $this->assertEquals('developer', $array['mageMode']);
    }

    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'type' => 'production',
            'mageMode' => 'production'
        ];

        $config = EnvironmentConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'type', 'production');
        $this->assertPropertyEquals($config, 'mageMode', 'production');
    }

    public function test_from_array_with_missing_fields_uses_defaults(): void
    {
        $data = [];

        $config = EnvironmentConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'type', 'development');
        $this->assertPropertyEquals($config, 'mageMode', 'developer');
    }

    public function test_from_array_with_partial_data_uses_defaults_for_missing(): void
    {
        $data = ['type' => 'staging'];

        $config = EnvironmentConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'type', 'staging');
        $this->assertPropertyEquals($config, 'mageMode', 'developer');
    }
}
