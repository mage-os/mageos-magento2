<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\LoggingConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for LoggingConfiguration VO
 */
class LoggingConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): LoggingConfiguration
    {
        return new LoggingConfiguration(
            debugMode: true,
            logLevel: 'debug'
        );
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function test_it_constructs_with_all_parameters(): void
    {
        $config = new LoggingConfiguration(
            debugMode: true,
            logLevel: 'debug'
        );

        $this->assertPropertyEquals($config, 'debugMode', true);
        $this->assertPropertyEquals($config, 'logLevel', 'debug');
    }

    public function test_it_constructs_with_debug_mode_disabled(): void
    {
        $config = new LoggingConfiguration(
            debugMode: false,
            logLevel: 'error'
        );

        $this->assertPropertyEquals($config, 'debugMode', false);
        $this->assertPropertyEquals($config, 'logLevel', 'error');
    }

    public function test_to_array_contains_all_fields(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('debugMode', $array);
        $this->assertArrayHasKey('logLevel', $array);
        $this->assertTrue($array['debugMode']);
        $this->assertEquals('debug', $array['logLevel']);
    }

    public function test_from_array_with_complete_data(): void
    {
        $data = [
            'debugMode' => true,
            'logLevel' => 'info'
        ];

        $config = LoggingConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'debugMode', true);
        $this->assertPropertyEquals($config, 'logLevel', 'info');
    }

    public function test_from_array_with_missing_fields_uses_defaults(): void
    {
        $data = [];

        $config = LoggingConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'debugMode', false);
        $this->assertPropertyEquals($config, 'logLevel', 'error');
    }

    public function test_supports_various_log_levels(): void
    {
        $levels = ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'];

        foreach ($levels as $level) {
            $config = new LoggingConfiguration(
                debugMode: false,
                logLevel: $level
            );

            $this->assertPropertyEquals($config, 'logLevel', $level);
        }
    }
}
