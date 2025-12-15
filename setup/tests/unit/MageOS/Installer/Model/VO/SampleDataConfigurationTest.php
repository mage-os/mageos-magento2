<?php

declare(strict_types=1);

namespace MageOS\Installer\Test\Unit\MageOS\Installer\Model\VO;

use MageOS\Installer\Model\VO\SampleDataConfiguration;
use MageOS\Installer\Test\TestCase\AbstractVOTest;

/**
 * Unit tests for SampleDataConfiguration VO
 */
class SampleDataConfigurationTest extends AbstractVOTest
{
    protected function createValidInstance(): SampleDataConfiguration
    {
        return new SampleDataConfiguration(install: true);
    }

    protected function getSensitiveFields(): array
    {
        return []; // No sensitive fields
    }

    public function test_it_constructs_with_install_true(): void
    {
        $config = new SampleDataConfiguration(install: true);

        $this->assertPropertyEquals($config, 'install', true);
    }

    public function test_it_constructs_with_install_false(): void
    {
        $config = new SampleDataConfiguration(install: false);

        $this->assertPropertyEquals($config, 'install', false);
    }

    public function test_to_array_contains_install_field(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('install', $array);
        $this->assertTrue($array['install']);
    }

    public function test_from_array_with_install_true(): void
    {
        $data = ['install' => true];

        $config = SampleDataConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', true);
    }

    public function test_from_array_with_install_false(): void
    {
        $data = ['install' => false];

        $config = SampleDataConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', false);
    }

    public function test_from_array_with_missing_field_defaults_to_false(): void
    {
        $data = [];

        $config = SampleDataConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', false);
    }
}
