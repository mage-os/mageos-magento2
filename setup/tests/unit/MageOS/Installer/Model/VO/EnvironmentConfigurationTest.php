<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\EnvironmentConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for EnvironmentConfiguration VO
 */
class EnvironmentConfigurationTest extends AbstractVOTest
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

    public function testItConstructsWithAllParameters(): void
    {
        $config = new EnvironmentConfiguration(
            type: 'production',
            mageMode: 'production'
        );

        $this->assertPropertyEquals($config, 'type', 'production');
        $this->assertPropertyEquals($config, 'mageMode', 'production');
    }

    public function testIsDevelopmentReturnsTrueForDevelopmentType(): void
    {
        $config = new EnvironmentConfiguration(
            type: 'development',
            mageMode: 'developer'
        );

        $this->assertTrue($config->isDevelopment());
        $this->assertFalse($config->isProduction());
    }

    public function testIsProductionReturnsTrueForProductionType(): void
    {
        $config = new EnvironmentConfiguration(
            type: 'production',
            mageMode: 'production'
        );

        $this->assertFalse($config->isDevelopment());
        $this->assertTrue($config->isProduction());
    }

    public function testToArrayContainsAllFields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('type', $array);
        $this->assertArrayHasKey('mageMode', $array);
        $this->assertEquals('development', $array['type']);
        $this->assertEquals('developer', $array['mageMode']);
    }

    public function testFromArrayWithCompleteData(): void
    {
        $data = [
            'type' => 'production',
            'mageMode' => 'production'
        ];

        $config = EnvironmentConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'type', 'production');
        $this->assertPropertyEquals($config, 'mageMode', 'production');
    }

    public function testFromArrayWithMissingFieldsUsesDefaults(): void
    {
        $data = [];

        $config = EnvironmentConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'type', 'development');
        $this->assertPropertyEquals($config, 'mageMode', 'developer');
    }

    public function testFromArrayWithPartialDataUsesDefaultsForMissing(): void
    {
        $data = ['type' => 'staging'];

        $config = EnvironmentConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'type', 'staging');
        $this->assertPropertyEquals($config, 'mageMode', 'developer');
    }
}
