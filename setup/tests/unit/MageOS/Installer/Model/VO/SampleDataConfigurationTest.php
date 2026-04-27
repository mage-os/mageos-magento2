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

    public function testItConstructsWithInstallTrue(): void
    {
        $config = new SampleDataConfiguration(install: true);

        $this->assertPropertyEquals($config, 'install', true);
    }

    public function testItConstructsWithInstallFalse(): void
    {
        $config = new SampleDataConfiguration(install: false);

        $this->assertPropertyEquals($config, 'install', false);
    }

    public function testToArrayContainsInstallField(): void
    {
        $config = $this->createValidInstance();
        $array = $config->toArray();

        $this->assertArrayHasKey('install', $array);
        $this->assertTrue($array['install']);
    }

    public function testFromArrayWithInstallTrue(): void
    {
        $data = ['install' => true];

        $config = SampleDataConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', true);
    }

    public function testFromArrayWithInstallFalse(): void
    {
        $data = ['install' => false];

        $config = SampleDataConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', false);
    }

    public function testFromArrayWithMissingFieldDefaultsToFalse(): void
    {
        $data = [];

        $config = SampleDataConfiguration::fromArray($data);

        $this->assertPropertyEquals($config, 'install', false);
    }
}
